<?php
namespace Deploy\Command;
use Qobo\Pattern\Pattern;

/**
 * SSH Command
 * 
 * @todo Implement interface
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class SSH {
	
	private static $transport = 'ssh %%host%% "%%command%%"';
	private static $command = 'if [ ! -d \"%%dir%%\" ] ; then mkdir -p \"%%dir%%\" ; fi && cd %%dir%% && %%command%%';

	public static function get() {
		$pattern = new Pattern(self::$transport, array('command' => self::$command));
		return (string) $pattern;
	}
}
