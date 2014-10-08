<?php
namespace Deploy;
use \Qobo\Pattern\Pattern;
/**
 * Command class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class Command {
 
	private $type;
	private $command;
	
	/**
	 * Constructor
	 * 
	 * @param string $type Type of the command, e.g.: install, update, etc
	 * @param Pattern $command Command line pattern with place holders
	 * @return object
	 */
	public function __construct($type, Pattern $command) {
		$this->type = $type;
		$this->command = $command;
	}

	/**
	 * Get command type
	 * 
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Get command line
	 * 
	 * @return Pattern
	 */
	public function getCommand() {
		return $this->command;
	}
	
	/**
	 * Run command
	 * 
	 * @param array $params Associative array of key-values to replace placedholders
	 * @return void
	 */
	public function run(array $params = array()) {
		$command = $this->command->parse($params);
		print "Executing: $command ... ";
		$out = array('');
		$result = exec($command, $out, $status);
		if ($status > 0) {
			print "FAIL\n";
			print $result . "\n";
		}
		else {
			print "OK\n";
			print $result . "\n";
		}
	}

}
?>
