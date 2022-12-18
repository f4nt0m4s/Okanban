<?php
namespace src\model;

use src\database\Database;
use src\model\Kanban;

class KanbanDAO {

	private static $CLASS_NAME = Kanban::class;
	private static $tableName = 'kanban';
	private static $columnNameId = 'id';
	private static $columnNameCreatedAt = 'created_at';
	private static $columnNameTitle = 'title';
	private static $columnNameDescription = 'description';
	private static $columnNameSlug = 'slug';
	private static $columnNameVisibility = 'visibility';
	private static $columnNameCreatorId = 'creator_id';
	/*
	 * Les classes authorisé à accèder aux nom des colonnes de la table 
	 * (elle même + classes qui sont en association(s) avec elle)
	*/
	private static $CLASS_AUTHORIZED = array(Kanban::class, User::class);
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

	public static function getColumnNameDescription($class) : string {
		return self::canAccess($class) ? self::$columnNameDescription : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameSlug($class) : string {
		return self::canAccess($class) ? self::$columnNameSlug : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameVisibility($class) : string {
		return self::canAccess($class) ? self::$columnNameVisibility : self::getColumnNameUnauthorized();
	}

	public static function getColumnNameCreatorId($class) : string {
		return self::canAccess($class) ? self::$columnNameCreatorId : self::getColumnNameUnauthorized();
	}

	public function insert(Kanban $kanban) : bool {
		if (is_null($this->db)) { return false; }
		if (is_null($kanban)) { return false; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$query = "INSERT INTO {$table} ("
				. self::getColumnNameCreatedAt($class) . ", " 
				. self::getColumnNameTitle($class) . ", " 
				. self::getColumnNameDescription($class) . ", " 
				. self::getColumnNameSlug($class) . ", " 
				. self::getColumnNameVisibility($class) . ", " 
				. self::getColumnNameCreatorId($class) . ") "
					. "VALUES ("
						. "'{$kanban->getCreatedAt()}', "
						. "'{$kanban->getTitle()}', "
						. "'{$kanban->getDescription()}', "
						. "'{$kanban->getSlug()}', "
						. "'{$kanban->getVisiblity()}', "
						. "'{$kanban->getCreatorId()}'"
					. ")";
		$count = $this->db->executeInsertUpdateDelete($query, null);
		return $count == 1;
	}

	public function delete(Kanban $kanban) : bool {
		if (is_null($this->db)) { return false; }
		if (is_null($kanban)) { return false; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameId = self::getColumnNameId($class);
		$query = "DELETE FROM {$table} WHERE {$table}.{$columnNameId} = ?";
		$count = $this->db->executeInsertUpdateDelete($query, $kanban->getId());
		return $count == 1;
	}

	public function findByTitle(string $title = '') : ?Kanban {
		if (is_null($this->db)) { return null; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameTitle = self::getColumnNameTitle($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameTitle} = ?";
		$result = $this->db->executeSelect($query, array($title), $class);
		return is_null($result) || empty($result) ? null : $this->map($result[0]);
	}

	public function findBySlug(string $slug = '') : ?Kanban {
		if (is_null($this->db)) { return null; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameSlug = self::getColumnNameSlug($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameSlug} = ?";
		$result = $this->db->executeSelect($query, array($slug), $class);
		return is_null($result) || empty($result) ? null : $this->map($result[0]);
	}

	public function findByVisibility(int $visibility = -1) : array {
		if (is_null($this->db)) { return array(); }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameVisibility = self::getColumnNameVisibility($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameVisibility} = ?";
		$result = $this->db->executeSelect($query, array($visibility), $class);
		return $this->fill($result);
	}

	public function findCreatorId(int $creatorId = -1) : ?Kanban {
		if (is_null($this->db)) { return null; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameCreatorId = self::getColumnNameCreatorId($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameCreatorId} = ?";
		$result = $this->db->executeSelect($query, array($creatorId), $class);
		return is_null($result) || empty($result) ? null : $this->map($result[0]);
	}

	// Récupère un kanban correspondant au titre et au createur (le créateur du kanban avec son titre)
	public function findByTitleCreatorId(string $title = '', int $creatorId = -1) : ?Kanban {
		if (is_null($this->db)) { return null; }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameTitle = self::getColumnNameTitle($class);
		$columnNameCreatorId = self::getColumnNameCreatorId($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameTitle} = ? AND {$table}.{$columnNameCreatorId} = ?";
		$result = $this->db->executeSelect($query, array($title, $creatorId), $class);
		return is_null($result) || empty($result) ? null : $this->map($result[0]);
	}

	public function findAllByCreatorId(int $creatorId = -1) : array {
		if (is_null($this->db)) { return array(); }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameCreatorId = self::getColumnNameCreatorId($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameCreatorId} = ?";
		$result = $this->db->executeSelect($query, array($creatorId), $class);
		return $this->fill($result);
	}

	public function findAllByCreatorIdOrderByColumnName(int $creatorId = -1, string $orderColumnName = '', string $order = 'ASC') : array {
		if (is_null($this->db)) { return array(); }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameCreatorId = self::getColumnNameCreatorId($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameCreatorId} = ?" 
				. " ORDER BY {$table}.{$orderColumnName} "
				. (strcmp($order, 'ASC') == 0 ? "ASC" : (strcmp($order, 'DESC') == 0 ? "DESC" : ""));
		$result = $this->db->executeSelect($query, array($creatorId), $class);
		return $this->fill($result);
	}

	public function findAllByIds(array $ids) {
		if (is_null($this->db)) { return array(); }
		foreach ($ids as $id) { if (!is_int(($id))) { return array(); } }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$columnNameId = self::getColumnNameId($class);
		$query = "SELECT * FROM {$table} WHERE {$table}.{$columnNameId} IN ";
		$query .= "(";
		for ($i=0; $i < count($ids) - 1; $i++) { 
			$query .= "?, ";
		}
		$query .= "?)";
		$result = $this->db->executeSelect($query, $ids, $class);
		return $this->fill($result);
	}
	/**
	 * Requête :
	 * SELECT * FROM kanban WHERE kanban.id IN (?, ?, ..., ?, ?) ORDER BY $columnName $order;
	 */
	public function findAllByIdsOrderByColumnName(array $ids, string $orderColumnName = '', string $order = 'ASC') : array {
		if (is_null($this->db)) { return array(); }
		foreach ($ids as $id) { if (!is_int(($id))) { return array(); } }
		$class = self::getClassName();
		$table = self::getTableName($class);
		$query = "SELECT * FROM {$table} WHERE {$table}." . self::getColumnNameId($class) . " IN ";
		$query .= "(";
		for ($i=0; $i < count($ids) - 1; $i++) { 
			$query .= "?, ";
		}
		$query .= "?)";
		$query .= " ORDER BY " . $table . "." . $orderColumnName . " "
				. (strcmp($order, 'ASC') == 0 ? "ASC" : (strcmp($order, 'DESC') == 0 ? "DESC" : ""));
		$result = $this->db->executeSelect($query, $ids, $class);
		return $this->fill($result);
	}

	private function fill($result) : array {
		if (!is_array($result)) { return array(); }
		$kanbans = array();
		$kanban = null;
		foreach ($result as $k => $v) {
			$kanban = $this->map($v);
			if (!is_null($kanban)) {
				array_push($kanbans, $kanban);
			}
		}
		return $kanbans;
	}
	
	private function map(Kanban $result) : ?Kanban {
		if (is_null($result)) { return null; }
		$kanban = new Kanban(
			intval($result->getId()),
			$result->getCreatedAt(),
			$result->getTitle(),
			$result->getDescription(),
			$result->getSlug(),
			$result->getVisiblity(),
			intval($result->getCreatorId())
		);
		return $kanban;
	}
}

?>