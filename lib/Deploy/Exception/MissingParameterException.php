<?php
namespace Deploy\Exception;

class MissingParameterException extends \InvalidArgumentException {

	public function __construct($parameter, $code = 0, Exception $previous = null) {
		parent::__construct("Missing required parameter: $parameter", $code, $previous);
	}
	
}
