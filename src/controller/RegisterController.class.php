<?php
namespace src\controller;

use src\form\validation\Validator;
use src\model\User;
use src\model\UserDAO;

class RegisterController {

	private $validator;
	private $param; // paramètres de POST = $_POST['username'] est un paramètre de la requête ?username=a mais comme c'est post c caché

	public function __construct($param) {
		$this->param = $param;
		$this->validator = $this->getValidator($param);
	}
	
	/**
	 * @param array $param
	 * @return Validator
	 */
	private function getValidator($param) : Validator {
		return (new Validator($param))
				->required('username', 'email', 'password', 'confirmpassword')
				->length('username', 3, 32)
				->email('email')
				->cmpPassword('password', 'confirmpassword')
				->password('password', 8, 1);
	}
	
	/**
	 * @return array<\src\form\validation\ValidationError> : la liste des erreurs des champs
	 */
	public function getValidatorError() {
		return $this->validator->getErrors();
	}
	/**
	 * @return bool : true si les champs sont valides, false sinon
	 */
	public function isValid() : bool {
		return $this->validator->isValid();
	}

	/**
	 * @return bool : true si l'enregistrement a été effectué, false sinon
	 */
	public function register() : bool {
		if (!$this->validator->isValid()) {
			return false;
		}
		$user = new User(-1, date("Y-m-d H:i:s"), $this->param['email'], $this->param['username'], password_hash($this->param['password'], PASSWORD_DEFAULT));
		$userDAO = new UserDAO();
		if (!is_null($userDAO->findByEmail($this->param['email']))) {
			return false;
		} else if (!is_null($userDAO->findByUsername($this->param['username']))) {
			return false;
		}
		if (!$userDAO->insert($user)) {
			return false;
		}
		return true;
	}
}
?>