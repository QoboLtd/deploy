<?php
namespace Deploy;
use Deploy\Config\iConfig;
/**
 * Project class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class Project {

	private $config;
	
	/**
	 * Constructor
	 * 
	 * @param Config $config Project configuration object
	 * @return object
	 */
	public function __construct(iConfig $config) {
		$this->config = $config;
	}

	/**
	 * Get project name
	 * 
	 * @return string
	 */
	public function getName() {
		$result = $this->config->getValue('project.name');

		return $result;
	}

	/**
	 * Get project config name
	 * 
	 * Sometimes project names are overlapping or confusing. Configuration
	 * names to the rescue.
	 * 
	 * @return string
	 */
	public function getConfigName() {
		return $this->config->getName();	
	}
	
	/**
	 * Run command
	 * 
	 * @param string $environmentName Name of the environment to run command at
	 * @param string $commandType Type of the command to run (e.g.: install, update, etc)
	 * @param array $params Optional parameters for command
	 * @return void
	 */
	public function run($environmentName, $commandType) {
		if (!$this->hasEnvironment($environmentName)) {
			throw new \InvalidArgumentException("This project has no configuration for environment [$environmentName]");
		}
		$environment = new Environment($this->config, $environmentName);
		$environment->run($commandType);
	}

	/**
	 * Check if current project has given environment
	 * 
	 * @param string $name Name of environment to check for
	 * @return boolean True if has, false otherwise
	 */
	public function hasEnvironment($name) {
		$result = false;

		$environment = $this->config->getValue('project.environments.' . $name);
		if (!empty($environment)) {
			$result = true;
		}

		return $result;
	}
	
	/**
	 * Get available environments
	 * 
	 * @return array
	 */
	public function getEnvironments() {
		$result = $this->config->getValue('project.environments');

		return $result;
	}

}
?>
