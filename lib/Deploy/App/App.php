<?php
namespace Deploy\App;

use \Deploy\Exception\MissingParameterException;

use \GetOptionKit\OptionCollection;
use \GetOptionKit\ContinuousOptionParser;
use \GetOptionKit\OptionPrinter\ConsoleOptionPrinter;

/**
 * App Class
 * 
 * @author Leonid Mamchenikov <l.mamchenkov@qobo.biz>
 */
class App {

	protected $argv;
	protected $result;

	/**
	 * Constructor
	 * 
	 * @param array $argv Options
	 */
	public function __construct($argv) {
		$this->argv = $argv;
		$this->result = array();
		$this->parseOptions();
	}

	/**
	 * Run app based on given parameters
	 * 
	 * @throws MissingParameterException
	 * @return array
	 */
	public function run() {
		if (empty($this->argv['tasks'])) {
			throw new MissingParameterException("task");
		}
		
		foreach ($this->argv['tasks'] as $taskName => $options) {
			$this->result[ $taskName ] = $this->runTask($taskName, $options);
		}

		return $this->result;
	}

	/**
	 * Get app run result
	 * 
	 * Return either all results or result for a given task
	 * 
	 * @param string $task (Optional) Task name
	 * @return mixed
	 */
	public function getResult($task = null) {
		$result = null;

		if (empty($task)) {
			$result = $this->result;
			return $result;
		}

		if (!empty($this->result[$task])) {
			return $result;
		}

		$result = $this->result[$taks];
		
		return $result;
	}

	/**
	 * Print app run result
	 * 
	 * Print either all results or result for a given task
	 * 
	 * @param string $task (Optional) Task name
	 * @return void
	 */
	public function printResult($task = null) {
		$result = $this->getResult($task);
		if (empty($task)) {
			foreach ($result as $task => $output) {
				print $output;
			}
		}
		else {
			print $result;
		}
	}

	/**
	 * Help message
	 * 
	 * @return string
	 */
	public static function help() {
		$result = '';
		
		$printer = new ConsoleOptionPrinter;
		list($app, $tasks) = self::getOptionsSpec();
		
		$result .= "\n\n";
		$result .= "USAGE: deploy [OPTIONS] TASK [TASK-OPTIONS]\n";
	
		$result .= "\n";
		$result .= "Main [OPTIONS] are:\n";
	
		$result .= $printer->render($app);

		$result .= "\nTask options are:\n";
		foreach ($tasks as $task => $details) {
			$result .=  "\n$task - " . $details['description'] . "\n";
			$result .=  $printer->render($details['specs']);
		}

		return $result;
	}
	
	/**
	 * Run a single task with given options
	 * 
	 * @throws \RuntimeException
	 * @param string $task Task name
	 * @param OptionCollection $options Options
	 * @return mixed
	 */
	protected function runTask($task, $options) {
		$result = null;
		
		$className = __NAMESPACE__ . '\\' . 'Task' . '\\' . ucfirst($task) . 'Task';
		if (!class_exists($className)) {
			throw new \RuntimeException("Task $task is not supported");
		}
		$task = new $className($options->toArray());
		$result = $task->run();

		return $result;
	}

	/**
	 * Get options spec
	 * 
	 * Get options specification for the app itself, and for each of the supported tasks.
	 * 
	 * @return array
	 */
	protected static function getOptionsSpec() {
		$result = array();
		
		$app = self::getOptionsAppSpec();
		$tasks = self::getOptionsTasksSpec();
		
		$result = array($app, $tasks);
		
		return $result;
	}

	/**
	 * Get options spec for the app
	 * 
	 * @return OptionCollection
	 */
	protected static function getOptionsAppSpec() {
		$result = new OptionCollection;
		return $result;
	}

	/**
	 * Get the list of available tasks
	 * 
	 * @todo Make this dynamic with tasks in Tasks/ folder
	 * @return array
	 */
	protected static function getTasks() {
		$result = array();

		$result['run'] = __NAMESPACE__ . '\Task\RunTask';
		$result['list'] = __NAMESPACE__ . '\Task\ListTask';
		$result['show'] = __NAMESPACE__ . '\Task\ShowTask';

		return $result;
	}

	/**
	 * Get options spec for supported tasks
	 * 
	 * @return array
	 */
	protected static function getOptionsTasksSpec() {
		$result = array();
		
		$tasks = self::getTasks();
		foreach ($tasks as $task => $taskClass) {
			$result[$task] = array();
			$result[$task]['description'] = $taskClass::getDescription();
			$result[$task]['specs'] = $taskClass::getParams();
		}

		return $result;
	}
	
	/**
	 * Parse options
	 * 
	 * @return void
	 */
	private function parseOptions() {
		$result = array();

		list($app, $tasks) = $this->getOptionsSpec();
		
		$tasks_specs = array();
		foreach ($tasks as $task => $options) {
			$tasks_specs[$task] = $options['specs'];
		}
		$tasks = array_keys($tasks_specs);

		$parser = new ContinuousOptionParser( $app );
		
		$result['app'] = $parser->parse( $this->argv );
		$result['tasks'] = array();
		
		$arguments = array();
		while( ! $parser->isEnd() ) {
			$taskIndex = array_search($parser->getCurrentArgument(), $tasks);
			
			if( $taskIndex !== false ) {
				$parser->advance();
				$task = $tasks[$taskIndex]; 
				unset($tasks[$taskIndex]);
				$parser->setSpecs( $tasks_specs[$task] );
				$result['tasks'][ $task ] = $parser->continueParse();
			} else {
				$arguments[] = $parser->advance();
			}
		}
		$this->argv = $result;
	}

}
