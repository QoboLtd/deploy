<?php
namespace Deploy;
/**
 * Environment class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class Environment {

	const CONFIG_PREFIX_ENVIRONMENT = 'environment-';

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
	public function __construct($name, Config $config) {

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
		$configSection = $this->config->getSection(self::CONFIG_PREFIX_ENVIRONMENT . $name);
		if (empty($configSection) || !is_array($configSection)) {
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
		if (empty($this->params['locations'])) {
			throw new \InvalidArgumentException("No locations configured for this environment");
		}
		if (!is_array($this->params['locations'])) {
			$this->params['locations'] = array($this->params['locations']);
		}

		$this->locations = new \SplObjectStorage();
		foreach ($this->params['locations'] as $location) {
			$this->locations->attach(new Location($location));
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
		
		if (empty($this->commands)) {
			$this->commands = array();
		}
		
		// First load default commands
		$configSection = $this->config->getSection('commands');
		if (!empty($configSection)) {
			foreach ($configSection as $commandType => $commandPattern) {
				$this->commands[$commandType] = new Command($commandType, $commandPattern);
			}
		}
		
		// Now override them with Environment commands
		foreach ($this->params['commands'] as $command) {
			if (!preg_match('/:/', $command)) {
				continue;
			}
			list($type, $command) = explode(':', $command);
			$this->commands[$type] = new Command($type, $command);
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

		$this->locations->rewind();
		while($this->locations->valid()) {
			$location = $this->locations->current();
			$location->run($command, $params);
			$this->locations->next();
		}
	}
}
?>
