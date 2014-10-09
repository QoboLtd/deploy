<?php
namespace Deploy;
use Deploy\Config\iConfig;
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
	public function __construct($type, $params) {
		$this->type = $type;
		$this->params = $params;
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
	public function run(Command $command, iConfig $config  = null) {

		// Wrap Environment command into the Location command first
		$newCommandString = \Deploy\Command\Factory::get($this->type);
		$newCommandString = (string) new Pattern($newCommandString, ['command' => $command->getCommand()]);
		
		$newCommandPattern = new Pattern($newCommandString);
		
		$params = array();
		$paramKeys = $newCommandPattern->getPlaceholders();
		if (!empty($paramKeys)) {
			foreach ($paramKeys as $paramKey) {
				if (isset($this->params->{$paramKey})) {
					$params[$paramKey] = $this->params->{$paramKey};
				}
				else {
					$configValue = $config->getValue($paramKey);
					if ($configValue) {
						$params[$paramKey] = $configValue;
					}
				}
			}
		}
		
		$newCommand = new Command($command->getType(), new Pattern($newCommandString));

		// Merge previous parameters from command line, project, environment and current location
		$newCommand->run($params);
	}

}
?>
