<?php
namespace Deploy\Runnable;
use Qobo\Pattern\Pattern;

class Command extends Runnable {

	protected $childrenClass = null;
	protected $childrenKey = null;
	
	protected $targetCheck = true;

	protected function render() {
		$result = '';

		if (empty($this->config['command'])) {
			return $result;
		}
		$params = array();
		if (!empty($this->config['params'])) {
			$params = $this->config['params'];
		}
		$pattern = new Pattern($this->config['command'], $this->config['params']);
		$result = $pattern->parse();
		
		return $result;
	}

	public function run() {
		if (!$this->isInTarget()) {
			return;
		}

		$command = $this->render();
		if (empty($command)) {
			return;
		}
		print "Executing: $command\n";
	}

	public function listChildren() {
		$result = array();
		
		$command = $this->render();
		if (empty($command)) {
			return $result;
		}
		
		$result = parent::listChildren();

		return $result;
	}
}
?>
