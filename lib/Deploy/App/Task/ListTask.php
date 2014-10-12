<?php
namespace Deploy\App\Task;

use \Deploy\Config\Factory;

class ListTask {

	protected $params;

	public function __construct($params) {
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
