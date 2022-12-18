<?php
namespace src\model;

use src\database\Database;
use src\model\Card;

class CardDAO {

	private static $CLASS_NAME = Card::class;
	private static $tableName = 'card';
	private static $columnNameId = 'id';
	private static $columnNameCreatedAt = 'created_at';
	private static $columnNameTitle = 'title';
	private static $columnNameKanbanId = 'kanban_id';
	/*
	 * Les classes authorisé à accèder aux nom des colonnes de la table 
	 * (elle même + classes qui sont en association(s) avec elle)
	*/
	private static $CLASS_AUTHORIZED = array(Card::class);
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

	public static function getColumnNameTitle($class) : string {
		return self::canAccess($class) ? self::$columnNameTitle : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameKanbanId($class) : string {
		return self::canAccess($class) ? self::$columnNameKanbanId : self::getColumnNameUnauthorized();
	}

	public function insert(Card $card) : bool {
		if (is_null($this->db)) { return false; }
		if (is_null($card)) { return false; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$query = "INSERT INTO {$table} ("
				. self::getColumnNameCreatedAt($class) . ", " 
				. self::getColumnNameTitle($class) . ", " 
				. self::getColumnNameKanbanId($class) . ") "
					. "VALUES ("
						. "'{$card->getCreatedAt()}', "
						. "'{$card->getTitle()}', "
						. "'{$card->getKanbanId()}'"
					. ")";
		$count = $this->db->executeInsertUpdateDelete($query, null);
		return $count == 1;
	}

	public function lastInsertId() : ?int {
		if (is_null($this->db)) { return null; }
		return $this->db->lastInsertId();
	}

	public function delete(Card $card) : bool {
		if (is_null($this->db)) { return false; }
		if (is_null($card)) { return false; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameId = self::getColumnNameId($class);
		$query = "DELETE FROM {$table} WHERE {$table}.{$columnNameId} = ?";
		$count = $this->db->executeInsertUpdateDelete($query, $card->getId());
		return $count == 1;
	}
	
	public function findById(int $id = -1) : ?Card {
		if (is_null($this->db)) { return null; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameId = self::getColumnNameId($class);
		$query = "SELECT * FROM {$table} WHERE $table.{$columnNameId} = ?";
		$result = $this->db->executeSelect($query, array($id), $class);
		return is_null($result) || empty($result) ? null : $this->map($result[0]);
	}

	public function findByTitleKanbanId(string $title, int $kanban_id) : ?Card {
		if (is_null($this->db)) { return null; }
				$class = self::getClassName();
		$table = self::getTableName($class);
		$query = "SELECT * FROM {$table} WHERE {$table}." . self::getColumnNameTitle($class) . " = ? " 
			. "AND {$table}." . self::getColumnNameKanbanId($class) . " = ?";
		$result = $this->db->executeSelect($query, array($title, $kanban_id), $class);
		return is_null($result) || empty($result) ? null : $this->map($result[0]);
	}

	public function findAllByKanbanId(int $kanban_id = -1) : array {
		if (is_null($this->db)) { return array(); }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameKanbanId = self::getColumnNameKanbanId($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameKanbanId} = ?";
		$result = $this->db->executeSelect($query, array($kanban_id), $class);
		return $this->fill($result);
	}

	private function fill($result) : array {
		if (!is_array($result)) { return array(); }
		$cards = array();
		$card = null;
		foreach ($result as $k => $v) {
			$card = $this->map($v);
			if (!is_null($card)) {
				array_push($cards, $card);
			}
		}
		return $cards;
	}

	private function map(Card $result) : ?Card {
		if (is_null($result)) { return null; }
		$card = new Card(
			intval($result->getId()),
			$result->getCreatedAt(),
			$result->getTitle(),
			intval($result->getKanbanId())
		);
		return $card;
	}
}

?>