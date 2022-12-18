<?php
namespace src\database;

use \PDO;
use \PDOException;
use \InvalidArgumentException;
use \RuntimeException;
use src\database\DotEnv;

class Database {

	private static $instance;
	private $pdo;
	
	private function __construct() {
		$this->pdo = null;
		self::$instance = null;
		$this->initialisation();
	}

	public function __destruct() {
		$this->pdo = null;
		self::$instance = null;
	}

	/**
	 * Initialisation de la connexion à la base de données
	 * @return void
	 */
	private function initialisation() {
		$dotEnv = null; 
		try {
			$dotEnv = new DotEnv(join(DIRECTORY_SEPARATOR, array(__DIR__, '.env')));
		} catch (InvalidArgumentException $e) {
			// echo $e->getMessage();
			return;
		}
		try {
			$dotEnv->load();
		} catch (RuntimeException $e) {
			// echo $e->getMessage();
			return;
		}
		$servername = getenv('DATABASE_SERVERNAME', false);
		$dbname = getenv('DATABASE_NAME', false);
		$username = getenv('DATABASE_USER', false);
		$password = getenv('DATABASE_PASSWORD', false);
		try {
			$this->pdo = new PDO("mysql:host=" . $servername . ";dbname=" . $dbname . "", $username, $password);
			$this->pdo->exec("set names utf8");
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			// echo $e->getMessage();
			$this->pdo = null;
			self::$instance = null;
		}
	}

	/**
	 * @return Database|null
	 */
	public static function getInstance() : ?Database {
		if (is_null(self::$instance)) {
			self::$instance = new Database();
		}
		$object = self::$instance;
		if ($object->pdo == null) {
			self::$instance = null;
		}
		return self::$instance;
	}

	/**
	 * Déconnexion de la base de données
	 * @return void
	 */
	public function close() : void {
		$this->pdo = null;
	}

	/**
	 * Exécution d'une requête select
	 * @return array La liste des tuples de la requête
	*/
	public function executeSelect($query, $parameters, $class) : array {
		if (is_null($this->pdo)) {
			return array();
		}
		$stmt = $this->pdo->prepare($query);
		$stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $class);
		$data = array();
		try {
			if (!is_null($parameters)) {
				$stmt->execute($parameters);
			} else {
				$stmt->execute();
			}
		} catch (PDOException $e) {
			// echo $e->getMessage();
			return array();
		}
		$data = array();
		$tuple = $stmt->fetch();
		if ($tuple) {
			while($tuple != false) {
				$data[] = $tuple;
				$tuple = $stmt->fetch();
			}
		}
		return $data;
	}

	/**
	 * Exécution d'une requête insert, update ou delete
	 * @return int Le nombre d'action(s) (insert, update ou delete) effectué(es)
	*/
	public function executeInsertUpdateDelete($query, $parameters) : int {
		if (is_null($this->pdo)) {
			return 0;
		}
		$stmt = $this->pdo->prepare($query);
		try {
			if (!is_null($parameters)) {
				$stmt->execute($parameters);
			} else {
				$stmt->execute();
			}
		} catch (PDOException $e) {
			// echo $e->getMessage();
			return 0;
		}
		return $stmt->rowCount();
	}

	/**
	 * @return int|null : l'identifiant de la dernière ligne insérée
	 */
	public function lastInsertId() : ?int {
		if (is_null($this->pdo)) {
			return null;
		}
		try {
			return intval($this->pdo->lastInsertId());
		} catch (PDOException $e) {
			return null;
		}
	}
}
?>