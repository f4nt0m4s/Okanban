<?php
namespace src\controller\form;

use src\form\validation\Validator;

class NewKanbanFormController {

	private static $keyForm = [
		'title' => 'title',
		'description' => 'description',
		'numbercolumns' => 'numbercolumns',
		'visibility' => 'visibility'
	];

	private $validator;
	private $param;

	public function __construct($param) {
		$this->param = $param;
		$this->validator = $this->getValidator($param);
	}

	public static function getKeyForm() : array {
		return self::$keyForm;
	}
	
	public static function getKeyFormTitle() : string {
		return self::$keyForm['title'];
	}

	public static function getKeyFormDescription() : string {
		return self::$keyForm['description'];
	}

	public static function getKeyFormNumberColumns() : string {
		return self::$keyForm['numbercolumns'];
	}

	public static function getKeyFormVisibility() : string {
		return self::$keyForm['visibility'];
	}

	private function getValidator($param) {
		return (new Validator($param))
				->required(self::getKeyFormTitle(), self::getKeyFormDescription(), self::getKeyFormNumberColumns(), self::getKeyFormVisibility())
				->notEmpty(self::getKeyFormTitle(), self::getKeyFormDescription(), self::getKeyFormNumberColumns(), self::getKeyFormVisibility())
				->length(self::getKeyFormTitle(), 3, 32)
				->length(self::getKeyFormDescription(), 3, 255)
				->number(self::getKeyFormNumberColumns());
	}

	public function getValidatorError() {
		return $this->validator->getErrors();
	}

	public function isValid() : bool {
		return $this->validator->isValid();
	}
}
?>