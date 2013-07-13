<?php

/// @returns A string version of an exception for display
/// @param $ex The exception instance
function exceptionToString($ex)
{
		$tmp = "Caught ".get_class($ex).": ".$ex->getMessage()."\n";
		$tmp .= "at line ".$ex->getLine()." in ".$ex->getFile()."\n";
		$tmp .= $ex->getTraceAsString();
		return $tmp;
}

/**
 * A base exception for all exceptions we define
 *
 * Add common exception features to this class so all custom exceptions have them.
 */
class BaseException extends Exception
{
	/// @returns A string version of the exception for display
	public function toString()
	{
		return exceptonToString($this);
	}

	/// @returns An HTML description of the exception for display
	public function toHtml()
	{
		return "<pre>".$this->toString()."</pre>";
	}
}
/// Connection to MES did not work
class MESConnectionFailedException extends BaseException { }

/// Database driver or engine reported an error
class DbException extends BaseException { }

/// Data in the database or the schema was in an inconsistent or invalid state
class DbHosedException extends DbException { }

/// An instance of a class could not be found
class InstanceNotFoundException extends BaseException { }

/// A member of a class could not be found
class MemberNotFoundException extends BaseException { }

/// A parameter or object was not unique when it should be
class DuplicateEntryException extends BaseException { }

/**
 * An error in coding or logic was found
 *
 * Do not use this exception for user or data caused errors. It is intended
 * solely for indicating static, design time errors.
 */
class DumbassDeveloperException extends BaseException { }

/// One or more model member values did not meet their constraints
class ConstraintFailedException extends BaseException { }

/// Failed to delete the object
class DeleteFailedException extends ConstraintFailedException 
{ 
  public function __construct($message, $_userMessage = NULL)
	{
		parent::__construct($message);
		$this->userMessage = $_userMessage;
	}
	
	public $userMessage;	///< The message displayed to the user
}

/// Failed to update the object
class UpdateFailedException extends ConstraintFailedException 
{ 
  public function __construct($message, $_userMessage = NULL)
	{
		parent::__construct($message);
		$this->userMessage = $_userMessage;
	}
	
	public $userMessage;	///< The message displayed to the user
}

/**
 * A parameter was not valid or did not meet constraint requirements
 *
 * When throwing this exception, construct it with a message that is acceptable
 * for display to a user so calling code can reuse it if needed. For example:
 * @code
 * The value is invalid.
 * @endcode
 * is not a good message.
 * @code
 * The field "password" must be more than 8 characters long.
 * @endcode
 * is better.
 */
class InvalidParameterException extends BaseException
{
	public function __construct($message, $_paramName = NULL, $_paramValue = NULL)
	{
		parent::__construct($message);
		$this->paramName = $_paramName;
		$this->paramValue = $_paramValue;
	}
	
	public $paramName;	///< The name of the invalid parameter
	public $paramValue;	///< The value of the invalid parameter
}

/**
 * Output an exception to the browser and web server error log
 *
 * This method overrides any output already buffered to send and only
 * displays the exception information.
 *
 * @param $ex The exception
 * @param $code Optional. HTTP error code to send. Defaults to 500 Internal Server Error
 */
function displayException($ex, $code = 500)
{
	$msg = exceptionToString($ex);
	
	error_log("PHP Error:");
	$lines = explode("\n", $msg);
	foreach($lines as $line)
		error_log("PHP    ".$line);

	ob_clean(); // clear anything we were going to send to the browser
	header('HTTP/1.1: '.$code);
	header('Status: '.$code);
	print("<pre>".$msg."</pre>");
}

