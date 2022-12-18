<?php
namespace src\model;

class Card {

	private $id;
	private $created_at;
	private $title;
	private $kanban_id;

	public function __construct(int $id = -1, string $created_at = '', string $title = '', int $kanban_id = -1) {
		$this->id = $id;
		$this->created_at = empty($created_at) ? date("Y-m-d H:i:s") : $created_at;
		$this->title = $title;
		$this->kanban_id = $kanban_id;
	}

	public function __destruct() {
		$this->id = null;
		$this->created_at = null;
		$this->title = null;
		$this->kanban_id = null;
		unset($this->id);
		unset($this->created_at);
		unset($this->title);
		unset($this->kanban_id);
	}

	public function getId() : int {
		return $this->id;
	}

	public function getCreatedAt() : string {
		return $this->created_at;
	}

	public function getTitle() : string {
		return $this->title;
	}

	public function getKanbanId() : int {
		return $this->kanban_id;
	}

	public function setId(int $id = -1) {
		$this->id = $id;
	}

	public function setCreatedAt(string $createdAt = '') {
		$this->created_at = $createdAt;
	}

	public function setTitle(string $title = '') {
		$this->title = $title;
	}

	public function setKanbanId(int $kanban_id = -1) {
		$this->kanban_id = $kanban_id;
	}

	public function toJSON() : array {
		/*return array(
			'id' => $this->getId(),
			'created_at' => $this->getCreatedAt(),
			'title' => $this->getTitle(),
			'kanban_id' => $this->getKanbanId()
		);*/
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