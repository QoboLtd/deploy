<?php
namespace Deploy\App\Task;

use \Deploy\Exception\MissingParameterException;
use \Deploy\Config\Factory;
use \Deploy\Runnable\Project;

use \GetOptionKit\OptionCollection;

class RunTask extends BaseTask {

	protected static $description = 'Run a deployment command';
	
	public function __construct(array $params = array()) {
		$this->params = $params;
		$this->validateParams();
	}

	/***
	 * Get command line options spec
	 * 
	 * @return OptionCollection
	 */
	public static function getParams() {
		$result = new OptionCollection;
		
		$result->add('t|test', 'test run only.')
			->isa('Boolean');
		
		$result->add('p|project:', 'project to deploy.')
			->isa('String')
			->required();
		
		$result->add('e|env:', 'environment to deploy.')
			->isa('String')
			->required();
		
		$result->add('c|command:', 'command to run.')
			->isa('String')
			->required();

		return $result;
	}

	protected function validateParams() {
		$requiredParams = array('project', 'env', 'command');
		foreach ($requiredParams as $requiredParam) {
			if (empty($this->params[$requiredParam])) {
				throw new MissingParameterException($requiredParam);
			}
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
