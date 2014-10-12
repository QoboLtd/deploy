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
		if (empty($this->argv['subCommands'])) {
			throw new \InvalidArgumentException("No commands given");
		}
		
		foreach ($this->argv['subCommands'] as $command => $options) {
			$className = __NAMESPACE__ . '\\' . 'Task' . '\\' . ucfirst($command) . 'Task';
			if (!class_exists($className)) {
				throw new \InvalidArgumentException("Subcommand $command is not supported");
			}
			$task = new $className($options->toArray());
			$task->run();
		}
	}

	public static function help() {
		$result = '';
		
		$printer = new ConsoleOptionPrinter;
		list($appspecs, $subcommand_specs) = self::getOptionsSpec();
		
		$result .= "\n\n";
		$result .= "USAGE: deploy [OPTIONS] COMMAND [COMMAND-OPTIONS]\n";
	
		$result .= "\n";
		$result .= "Main [OPTIONS] are:\n";
	
		$result .= $printer->render($appspecs);

		$result .= "\nCommand options are:\n";
		foreach ($subcommand_specs as $subcommand => $details) {
			$result .=  "\n$subcommand - " . $details['description'] . "\n";
			$result .=  $printer->render($details['specs']);
		}

		return $result;
	}
	
	protected static function getOptionsSpec() {
		$result = array();
		
		$app = self::getOptionsAppSpec();
		$subcommands = self::getOptionsSubcommandsSpec();
		
		$result = array($app, $subcommands);
		
		return $result;
	}

	protected static function getOptionsAppSpec() {
		$result = new OptionCollection;
		$result->add('v|verbose', 'verbose output.');
		return $result;
	}

	protected static function getOptionsSubcommandsSpec() {
		$result = array();
		
		// deploy run
		$run_cmdspecs = new OptionCollection;
		$run_cmdspecs->add('t|test', 'test run only.')
			->isa('Boolean');
		$run_cmdspecs->add('p|project:', 'project to deploy.')
			->isa('String')
			//->validValues(array('Factory', 'getList'))
			->required();
		$run_cmdspecs->add('e|env:', 'environment to deploy.')
			->isa('String')
			->required();
		$run_cmdspecs->add('c|command:', 'command to run.')
			->isa('String')
			->required();

		// deploy list
		$list_cmdspecs = new OptionCollection;
		//$list_cmdspecs->add('v');

		// deploy show
		$show_cmdspecs = new OptionCollection;
		$show_cmdspecs->add('p|project:', 'project to show')
			->isa('String')
			//->validValues(array('Factory', 'getList'))
			->required();


		$result = array(
			'run' => array(
				'description' => 'Run a deployment command', 
				'specs' => $run_cmdspecs
			),
			'list' => array(
				'description' => 'List available projects', 
				'specs' => $list_cmdspecs
			),
			'show' => array(
				'description' => 'Show project targets', 
				'specs' => $show_cmdspecs
			),
		);

		return $result;
	}
	
	private function parseOptions() {
		$result = array();

		list($app, $subcommands) = $this->getOptionsSpec();
		
		$subcommand_specs = array();
		foreach ($subcommands as $command => $options) {
			$subcommand_specs[$command] = $options['specs'];
		}
		$subcommands = array_keys($subcommand_specs);

		$parser = new ContinuousOptionParser( $app );
		
		$result['app'] = $parser->parse( $this->argv );
		$result['subCommands'] = array();
		
		$arguments = array();
		while( ! $parser->isEnd() ) {
			$subCommandIndex = array_search($parser->getCurrentArgument(), $subcommands);
			
			if( $subCommandIndex !== false ) {
				$parser->advance();
				$subcommand = $subcommands[$subCommandIndex]; 
				unset($subcommands[$subCommandIndex]);
				$parser->setSpecs( $subcommand_specs[$subcommand] );
				$result['subCommands'][ $subcommand ] = $parser->continueParse();
			} else {
				$arguments[] = $parser->advance();
			}
		}
		$this->argv = $result;
	}

}
