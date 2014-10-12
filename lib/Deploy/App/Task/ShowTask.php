<?php
namespace Deploy\App\Task;

use \Deploy\Config\Factory;
use \Deploy\Runnable\Project;

class ShowTask {

	protected $params;

	public function __construct(array $params = array()) {
		if (empty($params['project'])) {
			throw new \InvalidArgumentException("Missing required parameter: project");
		}
		$this->params = $params;
	}

	public function run() {
		
		$config = Factory::init($this->params['project']);
		$config = $config->data;
		
		$project = new Project($config);
			
		print "\n";
		print "Targets for project " . $this->params['project'] . ":\n\n";
		$children = $project->listChildren();
		$this->printOptions($children);

	}
	
	/**
	 * Recursively print available options
	 * 
	 * @param array $options Options to print
	 * @param integer $depth Indentation depth
	 * @return void
	 */
	protected function printOptions($options, $depth = 0) {
		foreach ($options as $name => $children) {
			if ($depth > 0) {
				list($type, $name) = explode(':', $name, 2);
				print str_repeat("\t", $depth) . '- ' . "$type $name\n";
			}
			$depth++;
			
			if (empty($children)) {
				continue;
			}
			foreach ($children as $child) {
				$this->printOptions($child, $depth);
			}
		}
	}

}
