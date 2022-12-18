<?php
namespace src\controller;

use src\Session;
use src\model\UserDAO;

class LoginController {

	private function __construct() {}
	public function __destruct() {}

	/**
	 * @param string $username : le nom d'utilisateur
	 * @param string $password : le mot de passe
	 * @return bool : true si la connexion est ok, false sinon
	 */
	public static function login(string $username, string $password) : bool {
		if (is_null($username) || is_null($password)) { return false; }
		$userDAO = new UserDAO();
		$user = $userDAO->findByUsername($username);
		if (!is_null($user)) {
			$hash = $user->getPassword();
			if (password_verify($password, $hash)) {
				// Regénération de l'identifiant de session (fixation session attack)
				session_regenerate_id();
				$_SESSION[Session::getUserIndex()] = serialize($user);
				return true;	
			}
		}
		return false;
	}

	/**
	 * Déconnexion
	 * @return void
	 */
	public static function logout() {
		unset($_SESSION[Session::getUserIndex()]);
		Session::destroy();
	}
	
}

?>