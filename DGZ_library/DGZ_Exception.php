<?php

namespace DGZ_library;

/**
 * Extension of Exception class so that more details can be passed through when
 * errors are encountered.
 *
 * @author Gustav Ndamukong
 */
class DGZ_Exception extends \Exception {

	const EXCEPTION = 'exception';
	const NO_MENU_DEFINED = 'noMenuDefined';
	const NO_LAYOUT_FOUND = 'noLayoutFound';
	const NO_VIEW_FOUND = 'noViewFound';
	const NO_VALIDATOR_FOUND = 'noValidatorFound';
	const QUERY_ERROR = 'queryError';
	const PERMISSION_DENIED = 'permissionError';
	const MISSING_PARAMETERS = 'missingParametersError';
	const IDENTIFIER_NOT_FOUND = 'identifierNotFound';
	const WRONG_PARAMETER_TYPE = 'wrongParameterType';
	const INCORRECT_USERNAME_PASSWORD = 'incorrectUsernameOrPassword';
	const NO_USER_RECORD = 'noUserRecord';
	const WRONG_ADAPTER_FOR_MODEL = 'wrongAdapterForModel';
	const MISSING_HANDLER_FOR_ACTION = 'missingHandlerForAction';
	const CONTROLLER_CLASS_NOT_FOUND = 'controllerClassNotFound';
	const INVALID_INPUT = 'invalidInput';
	const INVALID_CONFIG = 'invalidConfig';
	const INVALID_PARAMETER_VALUE = 'invalidParameterValue';
	const PHP_FATAL_ERROR = 'phpFatalError';
	const PHP_ERROR = 'phpError';
	const PHP_WARNING = 'phpWarning';
	const PHP_NOTICE = 'phpNotice';
	const PHP_OTHER_ERROR = 'phpOtherError';
	const DATABASE_QUERY_ERROR = 'databaseQueryError';
	const DATABASE_EXPECTED_RECORD_NOT_RETURNED = 'databaseExpectedRecordNotReturned';
	const NOT_IMPLEMENTED_EXCEPTION = 'notImplementedException';
	const NO_CONTEXT_PROVIDED = 'noContextProvided';
	const GALLERY_ALBUM_NOT_FOUND = 'galleryAlbumNotFoundException';

	protected $hint;
	protected $errorType;

	public function __construct($message, $errorType = self::EXCEPTION, $hint = 'No hint available') {

		if($errorType === self::NOT_IMPLEMENTED_EXCEPTION && $hint === 'No hint available') {
			$hint = 'The developer has either made blatant assumptions that they should not have done... or is lazy and hasn\'t implemented this functionality yet! :-)';
		}

		$this->hint = $hint;
		$this->errorType = $errorType;

		parent::__construct($message, 1, $this);

	}

	public function getHint() {
		return $this->hint;
	}

	public function getType() {
		return $this->errorType;
	}

	public function display() {
		try {
			$view = DGZ_View::getView('DGZExceptionView');
			$view->show($this);
		} catch (\Exception $e) {
			print $e->getMessage();
		}

	}





}
