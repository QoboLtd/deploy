<?php
namespace Deploy\Config;

class Factory {

	public static function init(\SplFileInfo $file) {
		$type = strtoupper($file->getExtension());

		if (empty($type)) {
			throw new InvalidArgumentException("Failed to determine configuration type for file [$file->getRealPath()]");
		}

		$className = __NAMESPACE__ . '\\' . $type;
		if (!class_exists($className)) {
			throw new \RuntimeException("Config type [$type] is not supported");
		}

		return new $className($file);
	}
}
?>
