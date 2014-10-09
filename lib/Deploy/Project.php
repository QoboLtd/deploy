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
	 * If project-name is configured in the main section, return
	 * that.  Otherwise, the name of the configuration itself.
	 * 
	 * @return string
	 */
	public function getName() {
		$resul = $this->config->getValue('project.name');

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
	public function run($environmentName, $commandType, array $params = array()) {
		if (!$this->hasEnvironment($environmentName)) {
			throw new \InvalidArgumentException("This project has no configuration for environment [$environmentName]");
		}
		$environment = new Environment($this->config, $environmentName);
		$environment->run($commandType, $params);
	}

	/**
	 * Check if current project has given environment
	 * 
	 * @param string $name Name of environment to check for
	 * @return boolean True if has, false otherwise
	 */
	private function hasEnvironment($name) {
		$result = false;

		$environment = $this->config->getValue('project.environments.' . $name);
		if (!empty($environment)) {
			$result = true;
		}

		return $result;
	}

}
?>
