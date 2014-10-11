<?php
namespace Deploy\Runnable;

class Location extends Runnable {

	protected $childrenClass = '\Deploy\Runnable\Command';
	protected $childrenKey = 'commands';

}
?>
