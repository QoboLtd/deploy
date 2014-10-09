<?php
namespace Deploy\Config;

class JSON extends Config {

	const VERSION = 1;

	public function __construct(\SplFileInfo $file) {
		if (!$this->isValidFile($file)) {
			throw new \InvalidArgumentException("Config file [" . $file->getRealPath() . "] failed validation");
		}
		
		$this->file = $file;
		
		$data = $this->parseFile($this->file);
		if (!$this->isValidData($data)) {
			throw new \InvalidArgumentException("Config file data in [" . $file->getRealPath() . "] failed validation");
		}

		$this->data = $data;
	}


	private function isValidFile(\SplFileInfo $file) {
		$result = false;

		if (!$file->isFile()) {
			throw new \InvalidArgumentException("File [" . $file->getRealPath() . "] is not a regular file");
		}

		if (!$file->isReadable()) {
			throw new \InvalidArgumentException("File [" . $file->getRealPath() . "] is not readable");
		}

		if ($file->getSize() <= 0) {
			throw new \InvalidArgumentException("File [" . $file->getRealPath() . "] is empty");
		}
		$result = true;

		return $result;
	}

	private function parseFile(\SplFileInfo $file) {
		$result = json_decode(file_get_contents($file->getRealPath()));
		if (empty($result)) {
			throw new \InvalidArgumentException("Failed to parse file [" . $file->getRealPath() . "]");
		}
		return $result;
	}

	private function isValidData(\stdClass $data) {
		$result = false;

		$this->validateFields($data);
		
		$result = true;

		return $result;
	}

	private function validateFields(\stdClass $data) {

		$fieldRules = array(
				'version' => '/' . self::VERSION . '/',
				'project.name' => '/\w+/',
			);

		foreach ($fieldRules as $field => $rule) {
			$value = $this->getValue($field, $data);
			if (!preg_match($rule, $value)) {
				throw new \InvalidArgumentException("Field [$field] failed validation for rule [$rule]");
			}
		}
	}

	public function getValue($property, \stdClass $data = null, $firstCall = true) {

		if (empty($data)) {
			$data = $this->data;
		}

		if ($firstCall && isset($data->{'user'}->{$property})) {
			return $data->{'user'}->{$property};
		}
		
		$currentProperty = $property;
		$newProperty = null;
		
		if (preg_match('#\.#', $property)) {
			list($currentProperty, $newProperty) = explode('.', $property, 2);
		}
		
		if (!isset($data->{$currentProperty})) {
			return null;
		}

		if (empty($newProperty)) {
			return $data->{$currentProperty};
		}

		if (is_object($data->{$currentProperty})) {
			return $this->getValue($newProperty, $data->{$currentProperty}, false);
		}

		return $data->{$currentProperty};
	}
}
?>
