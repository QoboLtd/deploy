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

		try {
			$project = new Project($config);
			$result = $project->run($options);
			$this->emailOk($result);
		} catch (\Exception $e) {
			$this->emailFail($e->getMessage());
			throw $e;
		}

		return $result;
	}

	/**
	 * Send success email
	 * 
	 * @param string $content
	 * @return boolean True on success, false otherwise
	 */
	public function emailOk($content) {
		$result = false;
		
		$to = empty($this->params['email-ok']) ? null : $this->params['email-ok'];
		if (!empty($to)) {
			$subject = 'Succes deploying ' . $this->params['project'] . ' to ' . $this->params['env'] . ' (' . $this->params['command'] . ')';
			$result = $this->sendMail($to, $subject, $content);
		}

		return $result;
	}

	/**
	 * Send fail email
	 * 
	 * @param string $content
	 * @return boolean True on success, false otherwise
	 */
	public function emailFail($content) {
		$result = false;
		
		$to = empty($this->params['email-fail']) ? null : $this->params['email-fail'];
		if (!empty($to)) {
			$subject = 'Failed deploying ' . $this->params['project'] . ' to ' . $this->params['env'] . ' (' . $this->params['command'] . ')';
			$result = $this->sendMail($to, $subject, $content);
		}

		return $result;
	}

	/**
	 * Send email
	 * 
	 * @param string $to Email recepient
	 * @param string $subject Email subject
	 * @param string $content Email body
	 * @return boolean True on success, false otherwise
	 */
	public function sendMail($to, $subject, $content) {
		$result = false;
		
		$from = empty($this->params['email-from']) ? null : $this->params['email-from'];
		if (empty($from)) {
			$processUser = posix_getpwuid(posix_geteuid());
			$from = $processUser['name'] . '@' . gethostname();
		}
		
		$transport = \Swift_SmtpTransport::newInstance('localhost', 25);
		$mailer = \Swift_Mailer::newInstance($transport);
		$message = \Swift_Message::newInstance($subject);
		$message->setTo($to);
		$message->setFrom($from);
		$message->setBody($content);
		
		if ($mailer->send($message)) {
			$result = true;
		}

		return $result;
	}
}
