<?php
// Load composer
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
/**
 * autoloader for namespaced classed
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 * @param string $class Class name, including namespace
 */
function nameSpaceAutoLoader($class) {
	$currentFolder = basename(dirname(__FILE__));
	
	$namespaces = explode('\\', $class);
	$currentNamespace = array_search($currentFolder, $namespaces);

	// A requested class is not from our namespace
	if ($currentNamespace === false) {
		return;
	}
	// A requested class consists only of our namespace and nothing else
	if (!isset($namespaces[ $currentNamespace + 1 ])) {
		return;
	}
	
	// Get namespace after the one which is current folder
	$namespaces = array_slice($namespaces, $currentNamespace + 1);
	
	// Prefix with full path and suffix with file extension
	$class = dirname(__FILE__) . DIRECTORY_SEPARATOR 
		. implode(DIRECTORY_SEPARATOR, $namespaces)
		. '.php';

	// Avoid errors and warnings in autoloaders stack
	if (file_exists($class)) {
		include_once($class);
	}
}

spl_autoload_register('nameSpaceAutoLoader');
?>
