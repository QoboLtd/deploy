<?php
namespace Deploy\Tests\Config;

use \Deploy\Config\Factory;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Deploy' . DIRECTORY_SEPARATOR . 'autoload.php';

class FactoryTest extends \PHPUnit_Framework_TestCase {

	public function dataProvider_badConfigFiles() {
		$result = array();
		$dir = new \DirectoryIterator(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'bad');
		foreach ($dir as $item) {
			if ($item->isDot()) {
				continue;
			}
			if (!$item->isFile()) {
				continue;
			}
			$result[] = array($item->getRealPath());
		}
		return $result;
	}
	
	public function dataProvider_goodConfigFiles() {
		$result = array();
		$dir = new \DirectoryIterator(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'good');
		foreach ($dir as $item) {
			if ($item->isDot()) {
				continue;
			}
			if (!$item->isFile()) {
				continue;
			}
			$result[] = array($item->getRealPath());
		}
		return $result;
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @dataProvider dataProvider_badConfigFiles
	 */
	public function test__init__failOnBadConfigFiles($configFile) {
		$config = Factory::init(new \SplFileInfo($configFile));
	}
	
	/**
	 * @dataProvider dataProvider_goodConfigFiles
	 */
	public function test__init__passOnGoodConfigFiles($configFile) {
		$config = Factory::init(new \SplFileInfo($configFile));
		$result = $config->getName();
		$this->assertFalse(empty($result));
	}

}
?>
