<?php
namespace src\utility;

class UtilityFunction {

	private function __construct() {}
	public function __destruct() {}

	public static function test_input(string $data) : string {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	public static function countDigit(string $str) {
		return preg_match_all("/[0-9]/", $str);
	}

	public static function plural(int $value) {
		return $value > 1 ? "s" : "";
	}

	public static function jsonMessageError(int $errorCode) : string {
		switch ($errorCode) {
			case JSON_ERROR_NONE:
				return 'No errors';
			case JSON_ERROR_DEPTH:
				return 'Maximum stack depth exceeded';
			case JSON_ERROR_STATE_MISMATCH:
				return 'Underflow or the modes mismatch';
			case JSON_ERROR_CTRL_CHAR:
				return 'Unexpected control character found';
			case JSON_ERROR_SYNTAX:
				return 'Syntax error, malformed JSON';
			case JSON_ERROR_UTF8:
				return 'Malformed UTF-8 characters, possibly incorrectly encoded';
			default:
				return 'Unknown error';
		}
	} 
}

?>
