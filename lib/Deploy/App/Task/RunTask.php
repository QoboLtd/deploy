<?php
namespace Deploy\App\Task;

use \Deploy\Config\Factory;
use \Deploy\Runnable\Project;

class RunTask {

	protected $params;

	public function __construct($params) {
		$this->params = $params;
		$this->validateParams();
	}

	protected function validateParams() {
		if (empty($this->params['project'])) {
			throw new \InvalidArgumentException("Missing required parameter: project");
		}
		if (empty($this->params['env'])) {
			throw new \InvalidArgumentException("Missing required parameter: env");
		}
		if (empty($this->params['command'])) {
			throw new \InvalidArgumentException("Missing required parameter: command");
		}
	}

	public function run() {
		$target = array();
		$target['project'] = array( $this->params['project'] );
		$target['environment'] = array( $this->params['env'] );
		$target['command'] = array( $this->params['command'] ); 

		$config = Factory::init($this->params['project']);
		$config = $config->data;
		$config[Project::CONFIG_KEY_TARGET] = $target;

		$options = array();
		if (!empty($this->params['test']) && $this->params['test']) {
			$options[Project::OPTION_KEY_TEST_ONLY] = true;
		}

		$project = new Project($config);
		$project->run($options);
	}
}
