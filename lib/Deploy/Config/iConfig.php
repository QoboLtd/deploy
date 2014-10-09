<?php
namespace Deploy\Config;
/**
 * iConfig interface
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
interface iConfig {

	/**
	 * Constructor
	 * 
	 * @param \SplFileInfo $file Config file object
	 * @return object
	 */
	public function __construct(\SplFileInfo $file);

	/**
	 * Get configuration name
	 * 
	 * @return string
	 */
	public function getName();

	/**
	 * Get configuration value
	 * 
	 * @param string $property Configuration property
	 * @param \stdClass $data (Optional) Configuration data
	 * @param boolean $firstCall Flag for recursion control
	 * @return mixed
	 */
	public function getValue($property, \stdClass $data = null, $firstCall = true);
}
?>
