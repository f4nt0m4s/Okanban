<?php

/**
 * Classe pour représenter les participants à une tache
 * table d'association taskuser
*/

namespace src\model;

class TaskUser {

	private $task_id;
	private $user_id;

	public function __construct(int $task_id = -1, int $user_id = -1) {
		$this->task_id = $task_id;
		$this->user_id = $user_id;
	}

	public function __destruct() {
		$this->task_id = null;
		$this->user_id = null;
	}

	public function getTaskId() : int {
		return $this->task_id;
	}

	public function getUserId() : int {
		return $this->user_id;
	}

	public function setTaskId(int $task_id = -1) {
		$this->task_id = $task_id;
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