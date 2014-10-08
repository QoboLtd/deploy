<?php
namespace Deploy;
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
	 * @param string $command Command line pattern with place holders
	 * @return object
	 */
	public function __construct($type, $command) {
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
	 * @return string
	 */
	public function getCommand() {
		return $this->command;
	}
	
	/**
	 * Populate pattern with values
	 * 
	 * @param string $pattern Pattern to parse
	 * @param array $params Key-values to use in pattern
	 * @return string
	 */
	public static function parsePattern($pattern, array $params = array()) {
		$result = $pattern;
		
		if (empty($params)) {
			return $result;
		}

		foreach ($params as $key => $value) {
			$key = '%%' . $key . '%%';
			$result = str_replace($key, $value, $result);
		}

		return $result;
	}
	/**
	 * Run command
	 * 
	 * @param array $params Associative array of key-values to replace placedholders
	 * @return void
	 */
	public function run(array $params = array()) {
		$command = $this->parsePattern($this->command, $params);
		print "Executing: $command\n";
	}

}
?>
