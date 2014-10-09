<?php
namespace Deploy\Tests;
use \Deploy\Project;
use \Deploy\Config\Factory;
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Deploy' . DIRECTORY_SEPARATOR . 'autoload.php';
/**
 * ProjectTest class
 *
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class ProjectTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Data provider of good configuration files
	 * 
	 * @return array
	 */
	public function dataProvider_goodConfigFiles() {
		$result = array();
		$dir = new \DirectoryIterator(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'good');
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
	 * Test that good configuration files are useful
	 * 
	 * @dataProvider dataProvider_goodConfigFiles
	 */
	public function test__getEnvironments__hasEnvironment($configFile) {
		$config = Factory::init(new \SplFileInfo($configFile));
		$project = new Project($config);

		$environments = $project->getEnvironments();
		$this->assertFalse(empty($environments));
		
		foreach ($environments as $name => $params) {
			$result = $project->hasEnvironment($name);
			$this->assertTrue($result);
		}
	}

	/**
	 * Test that bad environments cause an exception
	 * 
	 * @expectedException \InvalidArgumentException
	 * @dataProvider dataProvider_goodConfigFiles
	 */
	public function test__run__failOnBadEnvironment($configFile) {
		$config = Factory::init(new \SplFileInfo($configFile));	
		$project = new Project($config);

		$randomEnvironment = mt_rand();
		$this->assertFalse($project->hasEnvironment($randomEnvironment), "Random environment [$randomEnvironment] exists in config [$configFile]");

		$randomCommand = mt_rand();
		$result = $project->run($randomEnvironment, $randomCommand);
	}

}
?>
