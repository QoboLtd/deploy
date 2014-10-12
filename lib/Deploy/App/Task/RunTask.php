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
		if (empty($this->params['environment'])) {
			throw new \InvalidArgumentException("Missing required parameter: environment");
		}
		if (empty($this->params['command'])) {
			throw new \InvalidArgumentException("Missing required parameter: command");
		}
	}

	public function run() {
		$target = array();
		$target['project'] = array( $this->params['project'] );
		$target['environment'] = array( $this->params['environment'] );
		$target['command'] = array( $this->params['command'] ); 

		$config = Factory::init($this->params['project']);
		$config = $config->data;
		$config[Project::TARGET_KEY] = $target;

		$project = new Project($config);
		$project->run();
	}
}
