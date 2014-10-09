<?php
namespace Deploy\Config;
/**
 * Config class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
abstract class Config implements iConfig {

	/**
	 * File object
	 */
	protected $file;

	/**
	 * Parsed config data
	 */
	protected $data;
	
	/**
	 * Get configuration name
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->file->getFilename();
	}
	
	/**
	 * Add runtime user parameters to configuration
	 * 
	 * @todo This should probably be a different Config handler
	 * @param array $params List of parameters to add
	 * @return void
	 */
	public function addUserParams(array $params = array()) {
		if (empty($params)) {
			return;
		}

		$pairs = $this->convertToPairs($params);
		$this->data->{'user'} = (object) $pairs;
	}
	
	/**
	 * Convert parameters to pairs
	 * 
	 * This is a convenience method that helps to convert
	 * command line arguments given in a form of key=value into an
	 * associative array of parameters
	 * 
	 * @param array $params List of parameters
	 * @return array
	 */
	public static function convertToPairs(array $params = array()) {
		$result = array();

		if (empty($params)) {
			return $result;
		}

		foreach ($params as $item) {
			list($key, $value) = explode('=', $item, 2);
			$result[$key] = $value;
		}

		return $result;
	}
}
?>
