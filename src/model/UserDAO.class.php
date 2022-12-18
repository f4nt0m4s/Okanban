<?php
namespace src\model;

use src\database\Database;
use src\model\User;
use src\model\KanbanUser;
use src\model\KanbanUserDAO;
use src\model\TaskUser;

class UserDAO {

	private static $CLASS_NAME = User::class;
	private static $tableName = 'user';
	private static $columnNameId = 'id';
	private static $columnNameCreatedAt = 'created_at';
	private static $columnNameEmail = 'email';
	private static $columnNameUsername = 'username';
	private static $columnNamePassword = 'password';
	/*
	 * Les classes authorisé à accèder aux nom des colonnes de la table 
	 * (elle même + classes qui sont en association(s) avec elle)
	*/
	private static $CLASS_AUTHORIZED = array(Kanban::class, User::class, KanbanUser::class, TaskUser::class);
	private static $columnNameUnauthorized = 'undefined';
	private $db;

	public function __construct() {
		$this->db = Database::getInstance();
	}

	public function __destruct() {
		$this->db = null;
	}
	
	/**
	 * @return array : tableau contenant les classes autorisées à l'accès aux colonnes
	 */
	private static function getClassesAuthorized() : array {
		return self::$CLASS_AUTHORIZED;
	}
	
	/**
	 * @param object $class
	 * @return bool : true si l'accès est autorisé à cette classe, false sinon
	 */
	private static function canAccess($class) : bool {
		return in_array($class, self::getClassesAuthorized());
	}
	
	/**
	 * @return string : nom pour la colonne non autorisé
	 */
	private static function getColumnNameUnauthorized() : string {
		return self::$columnNameUnauthorized;
	}

	private static function getClassName() : string {
		return self::$CLASS_NAME;
	}

	public static function getTableName($class) : string {
		return self::canAccess($class) ? self::$tableName : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameId($class) : string {
		return self::canAccess($class) ? self::$columnNameId : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameCreatedAt($class) : string {
		return self::canAccess($class) ? self::$columnNameCreatedAt : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameEmail($class) : string {
		return self::canAccess($class) ? self::$columnNameEmail : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameUsername($class) : string {
		return self::canAccess($class) ? self::$columnNameUsername : self::getColumnNameUnauthorized();
	}

	public static function getColumnNamePassword($class) : string {
		return self::canAccess($class) ? self::$columnNamePassword : self::getColumnNameUnauthorized();
	}

	public function insert($user) : bool {
		if (is_null($this->db)) { return false; }
		if ($user == null) { return false; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$query = "INSERT INTO {$table} ("
				. self::getColumnNameCreatedAt($class) . ", " 
				. self::getColumnNameEmail($class) . ", " 
				. self::getColumnNameUsername($class) . ", " 
				. self::getColumnNamePassword($class) . ") "
					. "VALUES ("
						. "'{$user->getCreatedAt()}', "
						. "'{$user->getEmail()}', "
						. "'{$user->getUsername()}', "
						. "'{$user->getPassword()}'"
					. ")";
		$count = $this->db->executeInsertUpdateDelete($query, null);
		return $count == 1;
	}

	public function findById(int $id = -1) : ?User {
		if (is_null($this->db)) { return null; }
		if ($id == null) { return null; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameId = self::getColumnNameId($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameId} = ?";
		$result = $this->db->executeSelect($query, array($id), $class);
		return is_null($result) || empty($result) ? null : $this->map($result[0]);
	}
	
	public function findByEmail(string $email = '') : ?User {
		if (is_null($this->db)) { return null; }
		if ($email == null) { return null; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameEmail = self::getColumnNameEmail($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameEmail} = ?";
		$result = $this->db->executeSelect($query, array($email), $class);
		return is_null($result) || empty($result) ? null : $this->map($result[0]);
	}

	public function findByUsername(string $username = '') : ?User {
		if (is_null($this->db)) { return null; }
		if ($username == null) { return null; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameUsername = self::getColumnNameUsername($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameUsername} = ?";
		$result = $this->db->executeSelect($query, array($username), $class);
		return is_null($result) || empty($result) ? null : $this->map($result[0]);
	}

	public function findAllByKanbanId(int $kanban_id = -1) : array {
		if (is_null($this->db)) { return array(); }
		$class = self::getClassName();
		$tableUser = self::getTableName($class);
		$columnUserId = self::getColumnNameId($class);
		$tableKanbanUser = KanbanUserDAO::getTableName($class);
		$columnKanbanUserUserId = KanbanUserDAO::getColumnNameUserId($class);
		$columnKanbanUserKanbanId = KanbanUserDAO::getColumnNameKanbanId($class);
		$query = 
			"SELECT * FROM {$tableUser} INNER JOIN {$tableKanbanUser} ON "
			. "{$tableUser}.{$columnUserId} = {$tableKanbanUser}.{$columnKanbanUserUserId} "
			. "WHERE {$tableKanbanUser}.{$columnKanbanUserKanbanId} = ?";
		$result = $this->db->executeSelect($query, array($kanban_id), $class);
		return $this->fill($result);
	}

	public function findAllByTaskId(int $task_id = -1) : array {
		if (is_null($this->db)) { return array(); }
		$class = self::getClassName();
		$tableUser = self::getTableName($class);
		$columnUserId = self::getColumnNameId($class);
		$tableTaskUser = TaskUserDAO::getTableName($class);
		$columnTaskUserUserId = TaskUserDAO::getColumnNameUserId($class);
		$columnTaskUserTaskId = TaskUserDAO::getColumnNameTaskId($class);
		$query = 
			"SELECT * FROM {$tableUser} INNER JOIN {$tableTaskUser} ON "
			. "{$tableUser}.{$columnUserId} = {$tableTaskUser}.{$columnTaskUserUserId} "
			. "WHERE {$tableTaskUser}.{$columnTaskUserTaskId} = ?";
		$result = $this->db->executeSelect($query, array($task_id), $class);
		return $this->fill($result);
	}

	private function fill($result) : array {
		if (!is_array($result)) { return array(); }
		$users = array();
		$user = null;
		foreach ($result as $k => $v) {
			$user = $this->map($v);
			if (!is_null($user)) {
				array_push($users, $user);
			}
		}
		return $users;
	}

	private function map($result) : ?User {
		if (is_null($result)) { return null; }
		$user = new User(
			intval($result->getId()),
			$result->getCreatedAt(),
			$result->getEmail(),
			$result->getUsername(),
			$result->getPassword()
		);
		return $user;
	}
}
?>