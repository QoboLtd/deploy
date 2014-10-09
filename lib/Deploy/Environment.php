<?php
namespace Deploy;
use Deploy\Config\iConfig;
use Qobo\Pattern\Pattern;

/**
 * Environment class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class Environment {

	private $name;
	private $config;
	
	private $params;
	
	private $locations;
	private $commands;

	/**
	 * Constructor
	 * 
	 * @param string $name Environment name, e.g.: live, prp, dev, etc
	 * @param Config $config Project configuration object
	 * @return object
	 */
	public function __construct(iConfig $config, $name) {

		$this->name = $name;
		$this->config = $config;
		
		$this->loadParams($name);
		$this->loadLocations();
		$this->loadCommands();
	}
	
	/**
	 * Load environment configuration parameters
	 * 
	 * @throws InvalidArgumentException
	 * @param string $name Environment name
	 * @return void
	 */
	public function loadParams($name) {
		$configSection = $this->config->getValue('project.environments.' . $name);
		if (empty($configSection)) {
			throw new \InvalidArgumentException("Environment [$name] is not configured for this project");
		}
		$this->params = $configSection;
	}

	/**
	 * Load environment locations
	 * 
	 * @throws InvalidArgumentException
	 * @return void
	 */
	private function loadLocations() {

		$configSection = $this->config->getValue('project.environments.' . $this->name . '.locations');
		if (empty($configSection)) {
			throw new \InvalidArgumentException("No locations configured for this environment");
		}
		$this->locations = array();
		foreach ($configSection as $location) {
			$this->locations[] = new Location($location->{'type'}, $location->{'params'});
		}
	}

	/**
	 * Load command configuration
	 * 
	 * First, loads generic project-wide commands,
	 * and then overrides them with environment specific ones.
	 * 
	 * @return void
	 */
	private function loadCommands() {
		
		$this->commands = array();
		
		// First load default commands
		$configSection = $this->config->getValue('project.commands');
		if (!empty($configSection)) {
			foreach ($configSection as $commandType => $commandPattern) {
				$this->commands[$commandType] = new Command($commandType, new Pattern($commandPattern));
			}
		}
		
		// Now override them with Environment commands
		$configSection = $this->config->getValue('project.environments.' . $this->name . '.commands');
		if (!empty($configSection)) {
			foreach ($configSection as $commandType => $commandPattern) {
				$this->commands[$commandType] = new Command($commandType, new Pattern($commandPattern));
			}
		}
	}
	
	/**
	 * Run command for environment
	 * 
	 * This basically executes the command for each configured
	 * location of this environment
	 * 
	 * @param string $commandType Command type, e.g.: install, update, etc
	 * @param array $params Parameters for command
	 * @return void
	 */
	public function run($commandType, array $params = array()) {
		if (empty($this->commands) || !isset($this->commands[$commandType])) {
			throw new \InvalidArgumentException("Command [$commandType] is not configured for this environment");
		}
		$command = $this->commands[$commandType];

		foreach($this->locations as $location) {
			$location->run($command, $params);
		}
	}
}
?>
