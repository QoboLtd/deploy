<?php
namespace Deploy\Exception;
/**
 * ParseException
 * 
 * @author Leonid Mamchenkov <l.mamchenkov@qobo.biz>
 */
class ParseException extends \InvalidArgumentException {
	
	/**
	 * Constructor
	 * 
	 * @param string $message Parsing error message
	 * @param integer $code Code
	 * @param Exception $previous Previous exception
	 * @return object
	 */
	public function __construct($message, $code = 0, Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}
