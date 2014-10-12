<?php
namespace Deploy\App;

use \GetOptionKit\OptionCollection;
use \GetOptionKit\ContinuousOptionParser;
use \GetOptionKit\OptionPrinter\ConsoleOptionPrinter;

class App {

	private $argv;

	public function __construct($argv) {
		$this->argv = $argv;
		$this->parseOptions();
	}

	public function run() {
		if (empty($this->argv['tasks'])) {
			throw new \InvalidArgumentException("No tasks given");
		}
		
		foreach ($this->argv['tasks'] as $taskName => $options) {
			$className = __NAMESPACE__ . '\\' . 'Task' . '\\' . ucfirst($taskName) . 'Task';
			if (!class_exists($className)) {
				throw new \InvalidArgumentException("Task $taskName is not supported");
			}
			$task = new $className($options->toArray());
			$task->run();
		}
	}

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
	
	protected static function getOptionsSpec() {
		$result = array();
		
		$app = self::getOptionsAppSpec();
		$tasks = self::getOptionsTasksSpec();
		
		$result = array($app, $tasks);
		
		return $result;
	}

	protected static function getOptionsAppSpec() {
		$result = new OptionCollection;
		$result->add('v|verbose', 'verbose output.');
		return $result;
	}

	protected static function getOptionsTasksSpec() {
		$result = array();
		
		// deploy run
		$run = new OptionCollection;
		$run->add('t|test', 'test run only.')
			->isa('Boolean');
		$run->add('p|project:', 'project to deploy.')
			->isa('String')
			//->validValues(array('Factory', 'getList'))
			->required();
		$run->add('e|env:', 'environment to deploy.')
			->isa('String')
			->required();
		$run->add('c|command:', 'command to run.')
			->isa('String')
			->required();

		// deploy list
		$list = new OptionCollection;

		// deploy show
		$show = new OptionCollection;
		$show->add('p|project:', 'project to show')
			->isa('String')
			//->validValues(array('Factory', 'getList'))
			->required();

		$result = array(
			'run' => array(
				'description' => 'Run a deployment command', 
				'specs' => $run,
			),
			'list' => array(
				'description' => 'List available projects', 
				'specs' => $list,
			),
			'show' => array(
				'description' => 'Show project targets', 
				'specs' => $show,
			),
		);

		return $result;
	}
	
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
