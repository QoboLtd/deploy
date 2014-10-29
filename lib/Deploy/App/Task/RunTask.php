<?php
namespace Deploy\App\Task;

use \Deploy\Exception\MissingParameterException;
use \Deploy\Config\Factory;
use \Deploy\Runnable\Project;

use \GetOptionKit\OptionCollection;

/**
 * RunTask class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class RunTask extends BaseTask {

	protected static $description = 'Run a deployment command';
	
	/**
	 * Constructor
	 * 
	 * @param array $params Parameters for task run
	 * @return object
	 */
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
		
		$result->add('email-ok:', 'email to notify of success.')
			->isa('String');
		
		$result->add('email-fail:', 'email to notify of failure.')
			->isa('String');
		
		$result->add('email-from:', 'email to send from.')
			->isa('String');

		return $result;
	}

	/**
	 * Validate parameters
	 * 
	 * @throws MissingParameterException
	 * @return void
	 */
	protected function validateParams() {
		$requiredParams = array('project', 'env', 'command');
		foreach ($requiredParams as $requiredParam) {
			if (empty($this->params[$requiredParam])) {
				throw new MissingParameterException($requiredParam);
			}
		}
	}

	/**
	 * Run task
	 * 
	 * @throws Exception
	 * @return string
	 */
	public function run() {
		$result = '';
		
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
		$result = $project->run($options);

		return $result;
	}

}
