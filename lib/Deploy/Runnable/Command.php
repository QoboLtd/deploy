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

	public function run(array $options = array()) {
		if (!$this->isInTarget()) {
			return;
		}

		$command = $this->render();
		if (empty($command)) {
			return;
		}

		if (!empty($options[self::OPTION_KEY_TEST_ONLY]) && $options[self::OPTION_KEY_TEST_ONLY]) {
			print "[TEST] Executing: $command\n";
			return;
		}
		print "[REAL] Executing: $command\n";

		unset($output);
		$result = exec($command . ' 2>&1', $output, $return);
		$result = implode("\n", $output);
		$result .= "\n";
		if ($return > 0) {
			throw new \RuntimeException($result);
		}
		print $result;

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
