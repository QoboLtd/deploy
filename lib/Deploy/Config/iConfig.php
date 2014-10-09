<?php
namespace Deploy\Config;

interface iConfig {
	public function __construct(\SplFileInfo $file);
	public function getName();
	public function getValue($property, \stdClass $data = null);
}
?>
