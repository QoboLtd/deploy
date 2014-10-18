<?php
namespace Deploy\App\Task;

interface iTask {

	public function __construct(array $params = array());
	public function run();
	public static function getDescription();
	public static function getParams();
}
?>
