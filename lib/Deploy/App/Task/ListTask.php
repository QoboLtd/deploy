<?php
namespace Deploy\App\Task;

use \Deploy\Config\Factory;

class ListTask extends BaseTask{

	protected static $description = 'List available projects';

	public function __construct(array $params = array()) {
		$this->params = $params;
	}

	public function run() {
		$projects = Factory::getList();
		
		print "\n";
		print "Available projects:\n\n";
		foreach ($projects as $project) {
			print "\t- " . $project . "\n";
		}
	}
	
}
