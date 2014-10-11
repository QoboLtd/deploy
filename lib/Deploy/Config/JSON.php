<?php
namespace Deploy\Config;
/**
 * JSON class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class JSON extends Config {

	/**
	 * Constructor
	 * 
	 * @throws \InvalidArgumentException
	 * @param \SplFileInfo $file Config file object
	 * @return object
	 */
	public function __construct(\SplFileInfo $file) {
		if (!$this->isValidFile($file)) {
			throw new \InvalidArgumentException("Config file [" . $file->getRealPath() . "] failed validation");
		}
		
		$this->file = $file;
		
		$this->data = $this->parseFile($this->file);
	}
	
	/**
	 * Check if the given file is valid
	 * 
	 * @throws \InvalidArgumentException
	 * @param \SplFileInfo $file File object to check
	 * @return boolean True if valid, false otherwise
	 */
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
	
	/**
	 * Parse the given configuration file
	 * 
	 * @throws \InvalidArgumentException
	 * @param \SplFileInfo $file File object to check
	 * @return \stdClass
	 */
	private function parseFile(\SplFileInfo $file) {
		$result = json_decode(file_get_contents($file->getRealPath()), true);
		if (empty($result)) {
			throw new \InvalidArgumentException("Failed to parse file [" . $file->getRealPath() . "]");
		}
		return $result;
	}

}
?>
