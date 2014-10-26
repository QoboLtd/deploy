<?php
namespace Deploy\App\Task;

use \GetOptionKit\OptionCollection;

/**
 * BaseTask class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
abstract class BaseTask implements iTask {

	protected static $description;
	protected $params;
	
	/**
	 * Constructor
	 * 
	 * @param array $params Task run params
	 * @return object
	 */
	public function __construct(array $params = array()) {
		$this->params = $params;
	}

	/**
	 * Get task description
	 * 
	 * @return string
	 */
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
