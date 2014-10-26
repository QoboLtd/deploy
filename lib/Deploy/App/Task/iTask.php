<?php
namespace Deploy\App\Task;

/**
 * iTask interface
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
interface iTask {

	/**
	 * Constructor
	 * 
	 * @param array $params Parameters for task run
	 */
	public function __construct(array $params = array());
	
	/**
	 * Run task
	 * 
	 * @return mixed
	 */
	public function run();

	/**
	 * Get task description
	 * 
	 * @return string
	 */
	public static function getDescription();

	/**
	 * Get param specs
	 * 
	 * @return OptionCollection
	 */
	public static function getParams();
}
?>
