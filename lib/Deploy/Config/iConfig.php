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
}
?>
