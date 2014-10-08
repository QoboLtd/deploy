<?php
namespace Deploy;
/**
 * Config class
 * 
 * Project configuration
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class Config {

	const CONFIG_FILE_EXTENSION = 'ini';

	private $file;
	private $config;

	/**
	 * Constructor
	 * 
	 * @param string $file Path to configuration file
	 * @return object
	 */
	public function __construct($file) {
		$this->file = $file;
		$this->config = $this->read();
	}

	/**
	 * Get config name
	 * 
	 * This is a quick and easy way to refer to a configuration file
	 * 
	 * @return string
	 */
	public function getName() {
		return basename($this->file, '.' . self::CONFIG_FILE_EXTENSION);
	}
	
	/**
	 * Parse config file
	 * 
	 * Configuration file is expected to be in INI format,
	 * with sub-sections.
	 * 
	 * @throws InvalidArgumentException
	 * @param string $file Path to configuration file
	 * @return array
	 */
	public function read($file = null) {
		if (empty($file)) {
			$file = $this->file;
		}
		
		if (!file_exists($file)) {
			throw new \InvalidArgumentException("Config file [$file] does not exist");
		}

		if (!is_file($file)) {
			throw new \InvalidArgumentException("Config file [$file] is not a file");
		}

		if (!is_readable($file)) {
			throw new \InvalidArgumentException("Config file [$file] is not readable");
		}

		$result = parse_ini_file($file, true);
		if ($result === false) {
			throw new \InvalidArgumentException("Failed to parse INI file [$file]");
		}
		
		if (empty($result)) {
			throw new \InvalidArgumentException("Config file [$file] is empty");
		}

		return $result;
	}

	/**
	 * Get a single configuration section
	 * 
	 * @param string $section Name of the section to get
	 * @return array
	 */
	public function getSection($section) {
		$result = array();

		if (!empty($this->config[$section]) && is_array($this->config[$section])) {
			$result = $this->config[$section];
		}

		return $result;
	}

	/**
	 * Get a single configuration property
	 * 
	 * @param string $property Name of property to get
	 * @param string $section Section to get property from. Main assumed if omitted
	 * @return mixed
	 */
	public function getProperty($property, $section = 'main') {
		$result = null;

		if (empty($section)) {
			throw new \InvalidArgumentException("Configuration properties outside of sections are not supported");
		}

		$sectionConfig = $this->getSection($section);
		if (!empty($sectionConfig[$property])) {
			$result = $sectionConfig[$property];
		}

		return $result;
	}
	
	/**
	 * Get a list of all available projects
	 * 
	 * @param string $dir Path to folder with configuration files
	 * @return null|\SplObjectStorage
	 */
	public static function getProjects($dir) {
		$result = null;

		$dir = new \DirectoryIterator($dir);
		foreach ($dir as $item) {
			if ($item->isDot()) {
				continue;
			}
			
			if ($item->getExtension() <> self::CONFIG_FILE_EXTENSION) {
				continue;
			}
			
			if (empty($result)) {
				$result = new \SplObjectStorage();
			}
			
			$config = new Config($item->getPathname());
			$result->attach(new Project($config));
		}

		return $result;
	}
	
	/**
	 * Convert parameters to pairs
	 * 
	 * This is a convenience method that helps to convert
	 * command line arguments given in a form of key=value into an
	 * associative array of parameters
	 * 
	 * @param array $params List of parameters
	 * @return array
	 */
	public static function convertToPairs(array $params = array()) {
		$result = array();

		if (empty($params)) {
			return $result;
		}

		foreach ($params as $item) {
			list($key, $value) = explode('=', $item, 2);
			$result[$key] = $value;
		}

		return $result;
	}
}
?>
