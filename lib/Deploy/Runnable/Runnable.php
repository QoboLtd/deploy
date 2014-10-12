<?php
namespace Deploy\Runnable;

abstract class Runnable implements iRunnable {

	const CONFIG_KEY_TARGET = '_target';
	const OPTION_KEY_TEST_ONLY = 'test';
	
	protected $config;

	protected $childrenClass = 'Runnable';
	protected $childrenKey   = 'children';

	protected $targetCheck = false;

	public function __construct(array $config, array $parentConfig = array()) {
		$this->config = $config;
		$this->mergeWithParent($parentConfig);
	}

	protected function mergeWithParent($parentConfig) {
		// Nothing to merge, leave
		if (empty($parentConfig)) {
			return;
		}

		// Assume parent config, if there is nothing of ours
		if (empty($this->config)) {
			$this->config = $parentConfig;
			return;
		}

		// Thank god for this one!
		$this->config = array_replace_recursive($parentConfig, $this->config);
	}

	protected function isInTarget() {
		$result = true;

		// No target checking needed, so we assume yes.
		if (!$this->targetCheck) {
			return $result;
		}

		if (!isset($this->config[ self::CONFIG_KEY_TARGET ])) {
			throw new \RuntimeException("Target checking is required, but no target is set");
		}

		$target = $this->config[ self::CONFIG_KEY_TARGET ];
		if (!is_array($target)) {
			throw new \RuntimeException("Target checking is required, but target definition is invalid");
		}
		if (empty($target[ $this->config['type'] ])) {
			throw new \RuntimeException("Target checking is required, but target definition is empty for " . $this->config['type']);
		}
		
		if (!in_array($this->config['name'], $target[ $this->config['type'] ])) {
			$result = false;
		}

		return $result;
	}

	protected function getChildren() {
		$result = array();
		
		if (empty($this->config[ $this->childrenKey ])) {
			return;
		}

		if (empty($this->childrenClass)) {
			throw new \RuntimeException("No childrenClass given, while children exist");
		}

		if (!class_exists($this->childrenClass)) {
			throw new \RuntimeException("Given childrenClass [" . $this->childrenClass . "] does not exist");
		}

		$result = $this->config[ $this->childrenKey ];

		return $result;
	}

	public function run(array $options = array()) {
		if (!$this->isInTarget()) {
			return;
		}

		$children = $this->getChildren();
		if (empty($children)) {
			return;
		}
		
		foreach ($children as $name => $config) {
			$config['name'] = $name;
			$child = new $this->childrenClass($config, $this->config);
			$child->run($options);
		}
	}

	public function listChildren() {
		$result = array();
		
		$label = $this->config['type'] . ':' . $this->config['name'];
		$result[$label] = array();
		
		$children = $this->getChildren();
		if (empty($children)) {
			return $result;
		}

		foreach ($children as $name => $config) {
			$config['name'] = $name;
			$child = new $this->childrenClass($config, $this->config);
			$childResult = $child->listChildren();
			if (!empty($childResult)) {
				$result[$label][] = $childResult;
			}
		}

		return $result;
	}
}
