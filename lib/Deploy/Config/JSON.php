<?php
namespace Deploy\Config;
use \Deploy\Exception\ParseException;
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
		$this->validateFile($file);
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
	private function validateFile(\SplFileInfo $file) {
		if (!$file->isFile()) {
			throw new ParseException("File [" . $file->getRealPath() . "] is not a regular file");
		}
		if (!$file->isReadable()) {
			throw new ParseException("File [" . $file->getRealPath() . "] is not readable");
		}
		if ($file->getSize() <= 0) {
			throw new ParseException("File [" . $file->getRealPath() . "] is empty");
		}
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
			throw new ParseException("Failed to parse file [" . $file->getRealPath() . "]");
		}
		return $result;
	}

}
?>
