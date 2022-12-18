<?php
namespace src\model;

use src\database\Database;
use src\model\TaskUser;

class TaskUserDAO {

	private static $CLASS_NAME = TaskUser::class;
	private static $tableName = 'taskuser';
	private static $columnNameTaskId = 'task_id';
	private static $columnNameUserId = 'user_id';
	private static $CLASS_AUTHORIZED = array(TaskUser::class, Task::class, User::class);
	private static $columnNameUnauthorized = 'undefined';
	private $db;

	public function __construct() {
		$this->db = Database::getInstance();
	}

	public function __destruct() {
		$this->db = null;
	}

	private static function getClassesAuthorized() : array {
		return self::$CLASS_AUTHORIZED;
	}
	
	private static function canAccess($class) : bool {
		return in_array($class, self::getClassesAuthorized());
	}

	private static function getColumnNameUnauthorized() : string {
		return self::$columnNameUnauthorized;
	}

	private static function getClassName() : string {
		return self::$CLASS_NAME;
	}

	public static function getTableName($class) : string {
		return self::canAccess($class) ? self::$tableName : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameTaskId($class) : string {
		return self::canAccess($class) ? self::$columnNameTaskId : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameUserId($class) : string {
		return self::canAccess($class) ? self::$columnNameUserId : self::getColumnNameUnauthorized();
	}

	public function insert(TaskUser $taskUser) : bool {
		if (is_null($this->db)) { return false; }
		if (is_null($taskUser)) { return false; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$query = "INSERT INTO {$table} ("
					. self::getColumnNameTaskId($class) . ", "
					. self::getColumnNameUserId($class) . ") "
					. "VALUES ("
						. "'{$taskUser->getTaskId()}', "
						. "'{$taskUser->getUserId()}'"
					. ")";
		$count = $this->db->executeInsertUpdateDelete($query, null);
		return $count == 1;
	}

	public function delete(TaskUser $taskUser) : bool {
		if (is_null($this->db)) { return false; }
		if (is_null($taskUser)) { return false; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameTaskId = self::getColumnNameTaskId($class);
		$columnNameUserId = self::getColumnNameUserId($class);
		$query = "DELETE FROM {$table} WHERE {$table}.{$columnNameTaskId} = ? AND {$table}.{$columnNameUserId} = ?";
		$count = $this->db->executeInsertUpdateDelete($query, array($taskUser->getTaskId(), $taskUser->getUserId()));
		return $count == 1;
	}

	public function findAllByUserId(int $user_id = -1) : array {
		if (is_null($this->db)) { return array(); }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameUserId = self::getColumnNameUserId($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameUserId} = ?";
		$result = $this->db->executeSelect($query, array($user_id), $class);
		return $this->fill($result);
	}

	private function fill($result) : array {
		if (!is_array($result)) { return array(); }
		$taskUsers = array();
		$taskUser = null;
		foreach ($result as $k => $v) {
			$taskUser = $this->map($v);
			if (!is_null($taskUser)) {
				array_push($taskUsers, $taskUser);
			}
		}
		return $taskUsers;
	}

	private function map(TaskUser $result) : ?TaskUser {
		if (is_null($result)) { return null; }
		$taskUser = new TaskUser(
			intval($result->getTaskId()),
			intval($result->getUserId())
		);
		return $taskUser;
	}
}

?>