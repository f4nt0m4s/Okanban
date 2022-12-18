<?php 
namespace src;

use src\exception\ForbiddenException;

class Session {
	
	private static $USER_INDEX = 'user';
	
	private function __construct() {}
	public function __destruct() {}

	public static function getUserIndex() : string {
		return self::$USER_INDEX;
	}

	/**
	 * Démarrage de la session
	 * @return void
	 */
	public static function start() {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}

	/**
	 * Vérification que la session utilisateur est définit
	 * @throws ForbiddenException 
	 * @return void
	 */
	public static function checkSession() {
		self::start();
		if (!isset($_SESSION[self::getUserIndex()])) {
			throw new ForbiddenException();
		}
	}
	
	/**
	 * Arrêt de la session
	 * @return void
	 */
	public static function destroy() {
		self::start();
		session_destroy();
	}
}
?>