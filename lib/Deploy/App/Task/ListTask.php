<?php
namespace Deploy\App\Task;

use \Deploy\Config\Factory;

use \GetOptionKit\OptionCollection;

class ListTask extends BaseTask{

	protected static $description = 'List available projects';

	public function __construct(array $params = array()) {
		$this->params = $params;
	}

	public function run() {
		$projects = Factory::getList();
		asort($projects);
		
		if (!empty($this->params['simple']) && $this->params['simple']) {
			foreach ($projects as $project) {
				print "$project\n";
			}
		}
		else {
			print "\n";
			print "Available projects:\n\n";
			foreach ($projects as $project) {
				print "\t- " . $project . "\n";
			}
		}
	}
	
	/***
	 * Get command line options spec
	 * 
	 * @return OptionCollection
	 */
	public static function getParams() {
		$result = new OptionCollection;
		
		$result->add('s|simple', 'simple output (one per line)')
			->isa('Boolean');

		return $result;
	}


}
