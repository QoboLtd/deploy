<?php
namespace Deploy;
use Qobo\Pattern\Pattern;
/**
 * Location class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class Location {

	const LOCATION_TYPE_SSH = 'ssh';
	
	private $type;
	private $params;
	
	/**
	 * Constructor
	 * 
	 * @param string $location Location string in a form type:params
	 * @return object
	 */
	public function __construct($location) {
		$this->loadLocation($location);
	}

	/**
	 * Load location parameters from string
	 * 
	 * @param string $location Location string
	 * @return void
	 */
	private function loadLocation($location) {
		if (!preg_match('/:/', $location)) {
			throw new \InvalidArgumentException("Invalid location definition: [$location]");
		}
		$locationParts = explode(':', $location);
		$type = $locationParts[0];
		$params = array_slice($locationParts, 1);
		
		$this->type = $type;
		$this->params = $this->parseParams($type, $params);
	}

	/**
	 * Parse location parameters based on location type
	 * 
	 * @param string $type Location type, like ssh or ftp
	 * @param array $params Location parameters
	 * @return array
	 */
	private function parseParams($type, array $params) {
		$result = array();
		
		switch ($type) {
			case self::LOCATION_TYPE_SSH:
				$result['host'] = $params[0];
				$result['dir'] = $params[1];
				break;
			default:
				throw \InvalidArgumentException("Location type [$type] is not supported");
		}

		return $result;
	}

	/**
	 * Run location command
	 * 
	 * This is a wrapper with some trickery.  Take note.
	 * 
	 * @param Command $command Command to run
	 * @param array $params Parameters for the command to run
	 * @return void
	 */
	public function run(Command $command, array $params = array()) {

		// Wrap Environment command into the Location command first
		$newCommandString = \Deploy\Command\Factory::get($this->type);
		$newCommandPattern = new Pattern($newCommandString, ['command' => $command->getCommand()]);
		
		$newCommand = new Command($command->getType(), $newCommandPattern);

		// Merge previous parameters from command line, project, environment and current location
		$params = array_merge($params, $this->params);
		$newCommand->run($params);
	}

}
?>
