<?php
namespace Deploy\Config;
/**
 * Factory class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class Factory {

	/**
	 * Initialize configuration from given file object
	 * 
	 * @throws \InvalidArgumentException
	 * @param \SplFileInfo $file File object to use
	 * @return \iConfig
	 */
	public static function init(\SplFileInfo $file) {
		// Minimal check
		if (!$file->getRealPath() || !$file->getExtension()) {
			throw new \InvalidArgumentException("Invalid configuration file");
		}
		
		$type = strtoupper($file->getExtension());
		if (empty($type)) {
			throw new \InvalidArgumentException("Failed to determine configuration type for file [$file->getRealPath()]");
		}

		$className = __NAMESPACE__ . '\\' . $type;
		if (!class_exists($className)) {
			throw new \InvalidArgumentException("Config type [$type] is not supported");
		}

		return new $className($file);
	}
}
?>
