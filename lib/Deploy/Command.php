<?php
namespace Deploy;
use \Qobo\Pattern\Pattern;
/**
 * Command class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class Command {

	const STATUS_SUCCESS = 0;
 
	private $type;
	private $command;

	private $lastCommand;
	private $lastStatus;
	private $lastOutput;
	
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
	 * Get a fully formatted command line for previous run
	 * 
	 * @return string
	 */
	public function getLastCommand() {
		return $this->lastCommand;
	}
	
	/**
	 * Get status from the last run
	 * 
	 * @return integer
	 */
	public function getLastStatus() {
		return $this->lastStatus;
	}

	/**
	 * Get output from the last run
	 * 
	 * @return array
	 */
	public function getLastOutput() {
		return $this->lastOutput;
	}

	/**
	 * Run command
	 * 
	 * @throws RuntimeException
	 * @param array $params Associative array of key-values to replace placedholders
	 * @return void
	 */
	public function run(array $params = array()) {
		$this->lastCommand = $this->command->parse($params);
	   	$this->lastCommand .= ' 2>/dev/null'; // supress stderr output
		
		$this->lastOutput = array(''); // reset output
		
		$lastLine = exec($this->lastCommand, $this->lastOutput, $this->lastStatus);
		if ($this->lastStatus <> self::STATUS_SUCCESS) {
			throw new \RuntimeException("Non-zero exist status [$this->lastStatus] when executing command [$this->lastCommand]");
		}
	}

}
?>
