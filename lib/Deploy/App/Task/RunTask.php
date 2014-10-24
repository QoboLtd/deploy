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
		
		$result->add('email-ok:', 'email to notify of success.')
			->isa('String');
		
		$result->add('email-fail:', 'email to notify of failure.')
			->isa('String');
		
		$result->add('email-from:', 'email to send from.')
			->isa('String');

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

		try {
			$project = new Project($config);
			$output = $project->run($options);
			print $output;
			$this->emailOk($output);
		} catch (\Exception $e) {
			$this->emailFail($e->getMessage());
			throw $e;
		}
	}

	public function emailOk($content) {
		$to = empty($this->params['email-ok']) ? null : $this->params['email-ok'];
		if (!empty($to)) {
			$subject = 'Succes deploying ' . $this->params['project'] . ' to ' . $this->params['env'] . ' (' . $this->params['command'] . ')';
			$this->sendMail($to, $subject, $content);
		}
	}

	public function emailFail($content) {
		$to = empty($this->params['email-fail']) ? null : $this->params['email-fail'];
		if (!empty($to)) {
			$subject = 'Failed deploying ' . $this->params['project'] . ' to ' . $this->params['env'] . ' (' . $this->params['command'] . ')';
			$this->sendMail($to, $subject, $content);
		}
	}

	public function sendMail($to, $subject, $content) {
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
		$result = $mailer->send($message);
	}
}
