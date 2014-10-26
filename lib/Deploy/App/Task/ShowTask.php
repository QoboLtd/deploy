<?php
namespace Deploy\App\Task;

use \Deploy\Exception\MissingParameterException;
use \Deploy\Config\Factory;
use \Deploy\Runnable\Project;

use \GetOptionKit\OptionCollection;

/**
 * ShowTask class
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class ShowTask extends BaseTask {

	protected static $description = 'Show project targets';
	
	/**
	 * Constructor
	 * 
	 * @param array $params Task run params
	 * @return object
	 */
	public function __construct(array $params = array()) {
		if (empty($params['project'])) {
			throw new MissingParameterException('project');
		}
		$this->params = $params;
	}

	/**
	 * Run task
	 * 
	 * @return string
	 */
	public function run() {
		$result = '';
		
		$config = Factory::init($this->params['project']);
		$config = $config->data;
		
		$project = new Project($config);
			
		$result .= "\n";
		$result .= "Targets for project " . $this->params['project'] . ":\n\n";
		
		$children = $project->listChildren();
		$result .= $this->printOptions($children);

		return $result;
	}
	
	/***
	 * Get command line options spec
	 * 
	 * @return OptionCollection
	 */
	public static function getParams() {
		$result = new OptionCollection;
		
		$result->add('p|project:', 'project to show')
			->isa('String')
			->required();

		return $result;
	}

	/**
	 * Recursively print available options
	 * 
	 * @param array $options Options to print
	 * @param integer $depth Indentation depth
	 * @return string
	 */
	protected function printOptions($options, $depth = 0) {
		$result = '';
		
		foreach ($options as $name => $children) {
			if ($depth > 0) {
				list($type, $name) = explode(':', $name, 2);
				$result .= str_repeat("\t", $depth) . '- ' . "$type $name\n";
			}
			$depth++;
			
			if (empty($children)) {
				continue;
			}
			foreach ($children as $child) {
				$result .= $this->printOptions($child, $depth);
			}
		}

		return $result;
	}

}
