<?php
namespace Deploy\Tests\Config;
use \Deploy\Config\Factory;
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Deploy' . DIRECTORY_SEPARATOR . 'autoload.php';
/**
 * FactoryTest class
 *
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class FactoryTest extends \PHPUnit_Framework_TestCase {

	public function getBadConfigDirPath() {
		return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'bad'; 
	}
	
	public function getGoodConfigDirPath() {
		return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'good'; 
	}

	/**
	 * Data provider of bad configuration files
	 * 
	 * @return array
	 */
	public function dataProvider_badConfigFiles() {
		$result = array();
		$dir = new \DirectoryIterator($this->getBadConfigDirPath());
		foreach ($dir as $item) {
			if ($item->isDot()) {
				continue;
			}
			if (!$item->isFile()) {
				continue;
			}
			$result[] = array(Factory::getNameFromFile($item));
		}
		return $result;
	}
	
	/**
	 * Data provider of good configuration files
	 * 
	 * @return array
	 */

	public function dataProvider_goodConfigFiles() {
		$result = array();
		$dir = new \DirectoryIterator($this->getGoodConfigDirPath());
		foreach ($dir as $item) {
			if ($item->isDot()) {
				continue;
			}
			if (!$item->isFile()) {
				continue;
			}
			$result[] = array(Factory::getNameFromFile($item));
		}
		return $result;
	}

	/**
	 * Test that bad configuration files cause an exception
	 * 
	 * @expectedException \InvalidArgumentException
	 * @dataProvider dataProvider_badConfigFiles
	 */
	public function test__init__failOnBadConfigFiles($configFile) {
		$config = Factory::init($configFile, $this->getBadConfigDirPath());
	}
	
	/**
	 * Test that good configuration files are useful
	 * 
	 * @dataProvider dataProvider_goodConfigFiles
	 */
	public function test__init__passOnGoodConfigFiles($configFile) {
		$config = Factory::init($configFile, $this->getGoodConfigDirPath());
		$result = $config->getName();
		$this->assertFalse(empty($result));
	}

}
?>
