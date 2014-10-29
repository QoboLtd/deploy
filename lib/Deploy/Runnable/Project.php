<?php
namespace Deploy\Runnable;

class Project extends Runnable {

	protected $childrenClass = '\Deploy\Runnable\Environment';
	protected $childrenKey = 'environments';
	
	protected $sendEmail = true;
}
?>
