<?php
namespace src\model;

use src\database\Database;
use src\model\Task;
use src\model\TaskUser;
use src\model\Card;
use src\model\Kanban;
use src\model\CardDAO;
use src\model\KanbanDAO;

class TaskDAO {

	private static $CLASS_NAME = Task::class;
	private static $tableName = 'task';
	private static $columnNameId = 'id';
	private static $columnNameCreatedAt = 'created_at';
	private static $columnNameUpdatedAt = 'updated_at';
	private static $columnNameTitle = 'title';
	private static $columnNameDescription = 'description';
	private static $columnNameLimitDate = 'limit_date';
	private static $columnNameCardId = 'card_id';
	private static $columnNameCreatorId = 'creator_id';
	private static $CLASS_AUTHORIZED = array(Task::class, TaskUser::class);
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

	public static function getColumnNameId($class) : string {
		return self::canAccess($class) ? self::$columnNameId : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameCreatedAt($class) : string {
		return self::canAccess($class) ? self::$columnNameCreatedAt : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameUpdatedAt($class) : string {
		return self::canAccess($class) ? self::$columnNameUpdatedAt : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameTitle($class) : string {
		return self::canAccess($class) ? self::$columnNameTitle : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameDescription($class) : string {
		return self::canAccess($class) ? self::$columnNameDescription : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameLimitDate($class) : string {
		return self::canAccess($class) ? self::$columnNameLimitDate : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameCardId($class) : string {
		return self::canAccess($class) ? self::$columnNameCardId : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameCreatorId($class) : string {
		return self::canAccess($class) ? self::$columnNameCreatorId : self::getColumnNameUnauthorized();
	}

	public function insert(Task $task) : bool {
		if (is_null($this->db)) { return false; }
		if (is_null($task)) { return false; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$limit_date = is_null($task->getLimitDate()) || empty($task->getLimitDate()) ? "NULL" : "'{$task->getLimitDate()}'";
		$query = "INSERT INTO {$table} ("
				. self::getColumnNameCreatedAt($class) . ", " 
				. self::getColumnNameUpdatedAt($class) . ", " 
				. self::getColumnNameTitle($class) . ", " 
				. self::getColumnNameDescription($class) . ", " 
				. self::getColumnNameLimitDate($class) . ", " 
				. self::getColumnNameCardId($class) . ", " 
				. self::getColumnNameCreatorId($class) . ") "
					. "VALUES ("
						. "'{$task->getCreatedAt()}', "
						. "'{$task->getUpdatedAt()}', "
						. "'{$task->getTitle()}', "
						. "'{$task->getDescription()}', "
						. $limit_date . ", "
						. "'{$task->getCardId()}', "
						. "'{$task->getCreatorId()}'"
					. ")";
		$count = $this->db->executeInsertUpdateDelete($query, null);
		return $count == 1;
	}

	public function delete(Task $task) : bool {
		if (is_null($this->db)) { return false; }
		if (is_null($task)) { return false; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameId = self::getColumnNameId($class);
		$query = "DELETE FROM {$table} WHERE {$table}.{$columnNameId} = ?";
		$count = $this->db->executeInsertUpdateDelete($query, $task->getId());
		return $count == 1;
	}

	public function lastInsertId() : ?int {
		if (is_null($this->db)) { return null; }
		return $this->db->lastInsertId();
	}

	public function findById(int $id = -1) : ?Task {
		if (is_null($this->db)) { return null; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameId = self::getColumnNameId($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameId} = ?";
		$result = $this->db->executeSelect($query, array($id), $class);
		return is_null($result) || empty($result) ? null : $this->map($result[0]);
	}

	public function findByTitleCardId(string $title = '', int $card_id = -1) : ?Task {
		if (is_null($this->db)) { return null; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameTitle = self::getColumnNameTitle($class);
		$columnNameCardId = self::getColumnNameCardId($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameTitle} = ? AND {$table}.{$columnNameCardId} = ?";
		$result = $this->db->executeSelect($query, array($title, $card_id), $class);
		return is_null($result) || empty($result) ? null : $this->map($result[0]);
	}

	public function findAllByCardId(int $card_id = -1) : array {
		if (is_null($this->db)) { return array(); }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameCardId = self::getColumnNameCardId($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameCardId} = ?";
		$result = $this->db->executeSelect($query, array($card_id), $class);
		return $this->fill($result);
	}

	public function findAllByCreatorIdOrderByColumnName(int $creatorId = -1, string $orderColumnName = '', string $order = 'ASC') : array {
		if (is_null($this->db)) { return array(); }
		$class = self::getClassName();
		if (empty($orderColumnName)) { $orderColumnName = self::getColumnNameId($class); }
		$table = self::getTableName($class);
		$columnNameCreatorId = self::getColumnNameCreatorId($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameCreatorId} = ? " 
			. "ORDER BY " . $table . "." . $orderColumnName . " "
			. (strcmp($order, 'ASC') == 0 ? "ASC" : (strcmp($order, 'DESC') == 0 ? "DESC" : ""));
		$result = $this->db->executeSelect($query, array($creatorId), $class);
		return $this->fill($result);
	}

	/**
	 * Requête :
	 * SELECT task.* FROM task INNER JOIN card ON task.card_id = card.id INNER JOIN kanban ON card.kanban_id = ? ORDER BY task.$orderColumnName ASC/DESC
	*/
	public function findAllByKanbanIdOrderByColumnName(int $kanban_id = -1, string $orderColumnName = '', string $order = 'ASC') : array {
		if (is_null($this->db)) { return array(); }
		$class = self::getClassName();
		if (empty($orderColumnName)) { $orderColumnName = self::getColumnNameId($class); }
		$table = self::getTableName($class);
		$classCard = Card::class;
		$tableCard = CardDAO::getTableName($classCard);
		$classKanban = Kanban::class;
		$tableKanban = KanbanDAO::getTableName($classKanban);
		$query = "SELECT {$table}.* FROM {$table} INNER JOIN {$tableCard} ON {$table}."
			. self::getColumnNameCardId($class) . " = {$tableCard}." . CardDAO::getColumnNameId($classCard)
			. " INNER JOIN {$tableKanban} ON {$tableCard}." . CardDAO::getColumnNameKanbanId($classCard) . " = ?"
			. " ORDER BY " . $table . "." . $orderColumnName . " "
			. (strcmp($order, 'ASC') == 0 ? "ASC" : (strcmp($order, 'DESC') == 0 ? "DESC" : ""));
		$result = $this->db->executeSelect($query, array($kanban_id), $class);
		return $this->fill($result);
	}

	public function updateCardIdById(int $card_id = -1, int $id = -1) : bool {
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameId = self::getColumnNameId($class);
		$columnNameCardId = self::getColumnNameCardId($class);
		$query = "UPDATE {$table} SET {$table}.{$columnNameCardId} = ? WHERE {$table}.{$columnNameId} = ?";
		$result = $this->db->executeInsertUpdateDelete($query, array($card_id, $id));
		return $result == 1;
	}

	private function fill($result) : array {
		if (!is_array($result)) { return array(); }
		$tasks = array();
		$task = null;
		foreach ($result as $k => $v) {
			$task = $this->map($v);
			if (!is_null($task)) {
				array_push($tasks, $task);
			}
		}
		return $tasks;
	}

	private function map(Task $result) : ?Task {
		if (is_null($result)) { return null; }
		$task = new Task(
			intval($result->getId()),
			$result->getCreatedAt(),
			$result->getUpdatedAt(),
			$result->getTitle(),
			$result->getDescription(),
			$result->getLimitDate(),
			intval($result->getCardId()),
			intval($result->getCreatorId())
		);
		return $task;
	}
}

?>