<?php
namespace Deploy;
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
	public function __construct(Config $config) {
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
		$result = $this->getConfigName();

		$projectName = $this->config->getProperty('project-name');
		if (!empty($projectName)) {
			$result = $projectName;
		}

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
		$environment = new Environment($environmentName, $this->config);
		$environment->run($commandType, $params);
	}

}
?>
