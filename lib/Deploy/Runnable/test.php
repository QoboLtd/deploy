<?php
require_once 'iRunnable.php';
require_once 'Runnable.php';
require_once 'Project.php';
require_once 'Environment.php';
require_once 'Location.php';
require_once 'Command.php';

use Deploy\Runnable\Project;

$project = array(
	'type' => 'project',
	'name' => 'test.qobo.biz',
	'commands' => array(
		'install' => array('type' => 'command', 'command' => 'echo "Installing project"'),
		'upgrade' => array('type' => 'command', 'command' => 'echo "Upgrading project"'),
		'remove'  => array('type' => 'command', 'command' => 'echo "Removing project"'),
	),
	'environments' => array(
		'live' => array(
			'type' => 'environment',
			'locations' => array(
				'web1' => array(
					'type' => 'location',
					'commands' => array(
						'install' => array('type' => 'command', 'command' => 'git clone'),
						'update'  => array('type' => 'command', 'command' => 'git pull'),
						'remove'  => array('type' => 'command', 'command' => 'cd .. && rm -rf'),
					),
				),
			),
		),
		'dev' => array(
			'type' => 'environment',
			'name' => 'dev.test.qobo.biz',
			'locations' => array(
				'localhost' => array(
					'type' => 'location',
					'name' => 'localhost',
					'commands' => array(
						'remove' => array('type' => 'command',  'command' => 'git checkout master'),
					),
				),
			),
		),
	),
);

array_shift($argv);
if (count($argv) <> 3) {
	print "Usage: env loc command";
	die();
}
$target = array();
$target['environment'] = array($argv[0]);
$target['location'] = array($argv[1]);
$target['command'] = array($argv[2]);

$project['_target'] = $target;
print_r($target);

try {
	$pr = new \Deploy\Runnable\Project($project);
	$pr->run();
}
catch (Exception $e) {
	die("Deploy failed: " . $e->getMessage());
}
?>
