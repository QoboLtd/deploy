<?php
namespace Deploy\Config;
/**
 * Factory class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class Factory {

	const DEFAULT_DIR = 'etc/';

	/**
	 * Initialize configuration from given name
	 * 
	 * @throws \InvalidArgumentException
	 * @param string $name Configuration name
	 * @param string $dir Configuration folder
	 * @return \iConfig
	 */
	public static function init($name, $dir = self::DEFAULT_DIR) {
		$file = self::getFileFromName($name, $dir);
		
		if (empty($file)) {
			throw new \InvalidArgumentException("Invalid configuration file");
		}
		
		$type = self::getType($file);
		$className = self::getConfigClass($type);

		return new $className($file);
	}

	/**
	 * Get list of available configuration names
	 * 
	 * @param string $dir Configuration folder
	 * @return array
	 */
	public static function getList($dir = self::DEFAULT_DIR) {
		$result = array();
		
		$dir = new \DirectoryIterator($dir);
		foreach ($dir as $file) {
			if (!$file->isFile()) {
				continue;
			}

			if (!self::isValidFile($file)) {
				continue;
			}

			try {
				$type = self::getType($file);
				$className = self::getConfigClass($type);
			}
			catch (\Exception $e) {
				continue;
			}
			$result[] = self::getNameFromFile($file);
		}

		return $result;
	}

	/**
	 * Get configuration name from file
	 * 
	 * @param \SplFileInfo $file Configuration file
	 * @return string
	 */
	public static function getNameFromFile(\SplFileInfo $file) {
		$result = $file->getBasename('.' . $file->getExtension());
		return $result;
	}

	/**
	 * Get configuration file from name
	 * 
	 * @param string $name Configuration name
	 * @param string $dir Configuration folder
	 * @return null|\SplFileInfo
	 */
	public static function getFileFromName($name, $dir = self::DEFAULT_DIR) {
		$result = null;
		
		$dir = new \DirectoryIterator($dir);
		foreach ($dir as $file) {
			if (!$file->isFile()) {
				continue;
			}
			
			if (!self::isValidFile($file)) {
				continue;
			}
			
			$fileName = $file->getBasename('.' . $file->getExtension());
			if ($fileName <> $name) {
				continue;
			}
			try {
				$type = self::getType($file);
				$className = self::getConfigClass($type);
			}
			catch (\Exception $e) {
				continue;
			}
			$result = $file;
			break;
		}

		return $result;
	}

	/**
	 * Check if given file is a potentially valid configuration file
	 * 
	 * @param \SplFileInfo $file Configuration file object
	 * @return boolean True if valid, false otherwise
	 */
	protected static function isValidFile(\SplFileInfo $file) {
		$result = false;

		// Minimal check
		if ($file->getRealPath() && $file->getExtension()) {
			$result = true;
		}

		return $result;
	}
	
	/**
	 * Determine configuration class name from type
	 * 
	 * @throws \InvalidArgumentException
	 * @param string $type Configuration type
	 * @return string
	 */
	protected static function getConfigClass($type) {
		$result = __NAMESPACE__ . '\\' . $type;
		if (!class_exists($result)) {
			throw new \InvalidArgumentException("Config type [$type] is not supported");
		}
		return $result;
	}
	
	/**
	 * Determine configuration type from file
	 * 
	 * @throws \InvalidArgumentException
	 * @param \SplFileInfo $file Configuration file object
	 * @return string
	 */
	protected static function getType(\SplFileInfo $file) {
		$result = strtoupper($file->getExtension());
		if (empty($result)) {
			throw new \InvalidArgumentException("Failed to determine configuration type for file [$file->getRealPath()]");
		}
		return $result;
	}
}
?>
