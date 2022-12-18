<?php

/**
 * Classe pour représenter les participants au kanban
 * table d'association kanbanuser
*/

namespace src\model;

class KanbanUser {

	private $kanban_id;
	private $user_id;

	public function __construct(int $kanban_id = -1, int $user_id = -1) {
		$this->kanban_id = $kanban_id;
		$this->user_id = $user_id;
	}

	public function __destruct() {
		$this->kanban_id = null;
		$this->user_id = null;
	}

	public function getKanbanId() : int {
		return $this->kanban_id;
	}

	public function getUserId() : int {
		return $this->user_id;
	}

	public function setKanbanId(int $kanban_id = -1) {
		$this->kanban_id = $kanban_id;
	}

	public function setUserId(int $user_id = -1) {
		$this->user_id = $user_id;
	}

	public function toJSON() : array {
		$obj = $this;
		$reflect = new \ReflectionClass($obj);
		$properties = $reflect->getProperties();
		$arrayToJson = array();
		foreach ($properties as $p) {
			$p->setAccessible(true);
			$arrayToJson[strval($p->getName())] = $p->getValue($obj);
			$p->setAccessible(false);
		}
		json_encode($arrayToJson, JSON_UNESCAPED_UNICODE);
		$json = array();
		foreach ($arrayToJson as $k => $v) {
			$json[$k] = $v;
		}
		return $json;
	}

	public function __toString() {
		$obj = $this;
		$reflect = new \ReflectionClass($obj);
		$properties = $reflect->getProperties();
		$str = $reflect->getShortName() . " :\n";
		foreach ($properties as $p) {
			$p = $reflect->getProperty($p->getName());
			$p->setAccessible(true);
			$str .= "\t" . strval($p->getName()) . " : " . json_encode($p->getValue($obj), JSON_UNESCAPED_UNICODE) . "\n";
		}
		return $str;
	}

}


?>