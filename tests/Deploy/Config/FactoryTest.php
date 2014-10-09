<?php
namespace Deploy\Tests\Config;

use \Deploy\Config\Factory;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Deploy' . DIRECTORY_SEPARATOR . 'autoload.php';

class FactoryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function test__init__failNoExtension() {
		$config = Factory::init(new \SplFileInfo(__DIR__));
	}
	
	/**
	 * @expectedException \RuntimeException
	 */
	public function test__init__failTypeNotSupported() {
		$config = Factory::init(new \SplFileInfo(__FILE__));
	}

}
?>
