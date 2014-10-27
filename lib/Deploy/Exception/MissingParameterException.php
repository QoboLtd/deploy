<?php
namespace Deploy\Exception;
/**
 * MissingParameterException
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class MissingParameterException extends \InvalidArgumentException {

	/**
	 * Constructor
	 * 
	 * @param string $parameter Name of missing required parameter
	 * @param integer $code Code
	 * @param Exception $previous Previous exception
	 * @return object
	 */
	public function __construct($parameter, $code = 0, Exception $previous = null) {
		parent::__construct("Missing required parameter: $parameter", $code, $previous);
	}
}
