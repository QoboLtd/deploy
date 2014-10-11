<?php
namespace Deploy\Runnable;

interface iRunnable {

	public function __construct(array $config, array $parentConfig = array());
	public function run();
	
}
?>
