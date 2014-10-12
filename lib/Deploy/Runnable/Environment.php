<?php
namespace Deploy\Runnable;

class Environment extends Runnable {

	protected $childrenClass = '\Deploy\Runnable\Location';
	protected $childrenKey = 'locations';
	
	protected $targetCheck = true;
	
}
?>
