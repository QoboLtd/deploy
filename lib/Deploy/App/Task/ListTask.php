<?php
namespace Deploy\App\Task;

use \Deploy\Config\Factory;

use \GetOptionKit\OptionCollection;

/**
 * ListTask class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class ListTask extends BaseTask{

	protected static $description = 'List available projects';

	/**
	 * Run task
	 * 
	 * @return string
	 */
	public function run() {
		$result = '';
		
		$projects = Factory::getList();
		asort($projects);
		
		if (!empty($this->params['simple']) && $this->params['simple']) {
			foreach ($projects as $project) {
				$result .= "$project\n";
			}
		}
		else {
			$result .= "\n";
			$result .= "Available projects:\n\n";
			foreach ($projects as $project) {
				$result .= "\t- " . $project . "\n";
			}
		}

		return $result;
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
