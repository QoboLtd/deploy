<?php
namespace Deploy\Tests;

use \Deploy\Command;
use \Qobo\Pattern\Pattern;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Deploy' . DIRECTORY_SEPARATOR . 'autoload.php';

class CommandTest extends \PHPUnit_Framework_TestCase {

	public function test_getType() {
		$command = new Command('foo', new Pattern('bar'));
		$result = $command->getType();
		$this->assertEquals('foo', $result);
	}
	
	public function test_getCommand() {
		$command = new Command('foo', new Pattern('bar'));
		$result = $command->getCommand()->parse();
		$this->assertEquals('bar', $result);
	}

	public function test_run__normal() {
		$command = new Command('ok', new Pattern('ls'));
		$command->run();
		$result = $command->getLastStatus();
		$this->assertEquals(Command::STATUS_SUCCESS, $result);
	}
	/**
	 * @expectedException \RuntimeException
	 */
	public function test_run__failure() {
		$command = new Command('fail', new Pattern(date('Y-m-d')));
		$command->run();
	}

}
?>
