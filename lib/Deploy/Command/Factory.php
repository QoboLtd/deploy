<?php
namespace Deploy\Command;
/**
 * Factory class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class Factory {

	/**
	 * Get command by type
	 * 
	 * @param string $type Command type
	 * @return string
	 */
	public static function get($type) {
		$result = '';
		
		$type = strtoupper((string) $type);
		$commandClass = __NAMESPACE__ . '\\' . $type;
		if (!class_exists($commandClass)) {
			throw new \RuntimeException("Command type [$type] is not supported");
		}

		$result = $commandClass::get();

		return $result;
	}
}
?>
