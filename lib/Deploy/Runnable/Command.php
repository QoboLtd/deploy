<?php
namespace Deploy\Runnable;

class Command extends Runnable {

	protected $childrenClass = null;
	protected $childrenKey = null;
	
	protected $targetCheck = true;

	public function run() {
		if ($this->isInTarget()) {
			print $this->config['name'] . ': ' . $this->config['command'] . "\n";
		}
	}
}
?>
