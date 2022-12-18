<?php
namespace src\model;

use src\database\Database;
use src\model\KanbanUser;
use src\model\Kanban;
use src\model\User;

class KanbanUserDAO {

	private static $CLASS_NAME = KanbanUser::class;
	private static $tableName = 'kanbanuser';
	private static $columnNameKanbanId = 'kanban_id';
	private static $columnNameUserId = 'user_id';
	private static $CLASS_AUTHORIZED = array(KanbanUser::class, Kanban::class, User::class);
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

	public static function getColumnNameKanbanId($class) : string {
		return self::canAccess($class) ? self::$columnNameKanbanId : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameUserId($class) : string {
		return self::canAccess($class) ? self::$columnNameUserId : self::getColumnNameUnauthorized();
	}

	public function insert(KanbanUser $kanbanUser) : bool {
		if (is_null($this->db)) { return false; }
		if (is_null($kanbanUser)) { return false; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$query = "INSERT INTO {$table} ("
				. self::getColumnNameKanbanId($class) . ", " 
				. self::getColumnNameUserId($class) . ") "
					. "VALUES ("
						. "'{$kanbanUser->getKanbanId()}', "
						. "'{$kanbanUser->getUserId()}'"
					. ")";
		$count = $this->db->executeInsertUpdateDelete($query, null);
		return $count == 1;
	}

	public function delete(KanbanUser $kanbanUser) : bool {
		if (is_null($this->db)) { return false; }
		if (is_null($kanbanUser)) { return false; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameKanbanId = self::getColumnNameKanbanId($class);
		$columnNameUserId = self::getColumnNameUserId($class);
		$query = "DELETE FROM {$table} WHERE {$table}.{$columnNameKanbanId} = ? AND {$table}.{$columnNameUserId} = ?";
		$count = $this->db->executeInsertUpdateDelete($query, array($kanbanUser->getKanbanId(), $kanbanUser->getUserId()));
		return $count == 1;
	}

	public function find(KanbanUser $kanbanUser) : ?KanbanUser {
		if (is_null($this->db)) { return null; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$query = "SELECT * FROM {$table} WHERE {$table}." . self::getColumnNameKanbanId($class) . " = ? AND {$table}." . self::getColumnNameUserId($class) . " = ?";
		$result = $this->db->executeSelect($query, array($kanbanUser->getKanbanId(), $kanbanUser->getUserId()), $class);
		return is_null($result) || empty($result) ? null : $this->map($result[0]);
	}

	public function findAllByUserId($user_id = -1) : array {
		if (is_null($this->db)) { return array(); }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$query = "SELECT * FROM {$table} WHERE {$table}." . self::getColumnNameUserId($class) . " = ?";
		$result = $this->db->executeSelect($query, array($user_id), $class);
		return $this->fill($result);
	}

	private function fill($result) : array {
		if (!is_array($result)) { return array(); }
		$kanbansUser = array();
		$kanbanUser = null;
		foreach ($result as $k => $v) {
			$kanbanUser = $this->map($v);
			if (!is_null($kanbanUser)) {
				array_push($kanbansUser, $kanbanUser);
			}
		}
		return $kanbansUser;
	}

	private function map(KanbanUser $result) : ?KanbanUser {
		if (is_null($result)) { return null; }
		$kanbanUser = new KanbanUser(
			intval($result->getKanbanId()),
			intval($result->getUserId())
		);
		return $kanbanUser;
	}
}

?>