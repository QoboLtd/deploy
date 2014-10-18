<?php
namespace Deploy\App\Task;

use \GetOptionKit\OptionCollection;

abstract class BaseTask implements iTask {

	protected static $description;
	protected $params;

	public static function getDescription() {
		return static::$description;
	}
	
	/***
	 * Get command line options spec
	 * 
	 * @return OptionCollection
	 */
	public static function getParams() {
		$result = new OptionCollection;
		return $result;
	}

}
?>
