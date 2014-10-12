<?php
namespace Deploy\Runnable;

class Command extends Runnable {

	protected $childrenClass = null;
	protected $childrenKey = null;
	
	protected $targetCheck = true;

	public function run() {
		if (!$this->isInTarget()) {
			return;
		}
		if (empty($this->config['command'])) {
			return;
		}
		print $this->config['name'] . ': ' . $this->config['command'] . "\n";
	}

	public function listChildren() {
		$result = array();
		
		if (empty($this->config['command'])) {
			return $result;
		}
		
		$result = parent::listChildren();

		return $result;
	}
}
?>
