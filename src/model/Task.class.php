<?php
namespace src\model;

class Task {

	private $id;
	private $created_at;
	private $updated_at;
	private $title;
	private $description;
	private $limit_date;
	private $card_id;
	private $creator_id;

	public function __construct(int $id = -1, string $created_at = '', string $updated_at = '', string $title = '', string $description = '', string $limit_date = null, int $card_id = -1, int $creator_id = -1) {
		$this->id = $id;
		$this->created_at = empty($created_at) ? date("Y-m-d H:i:s") : $created_at;
		$this->updated_at = empty($updated_at) ? date("Y-m-d H:i:s") : $updated_at;
		$this->title = $title;
		$this->description = $description;
		$this->limit_date = $limit_date;
		$this->card_id = $card_id;
		$this->creator_id = $creator_id;
	}

	public function __destruct() {
		$this->id = null;
		$this->created_at = null;
		$this->updated_at = null;
		$this->title = null;
		$this->description = null;
		$this->limit_date = null;
		$this->card_id = null;
		$this->creator_id = null;
		unset($this->id);
		unset($this->created_at);
		unset($this->updated_at);
		unset($this->title);
		unset($this->description);
		unset($this->limit_date);
		unset($this->card_id);
		unset($this->creator_id);
	}

	public function getId() : int {
		return $this->id;
	}

	public function getCreatedAt() : string {
		return $this->created_at;
	}

	public function getUpdatedAt() : string {
		return $this->updated_at;
	}

	public function getTitle() : string {
		return $this->title;
	}

	public function getDescription() : string {
		return $this->description;
	}

	public function getLimitDate() : ?string {
		return $this->limit_date;
	}

	public function getCardId() : int {
		return $this->card_id;
	}

	public function getCreatorId() : int {
		return $this->creator_id;
	}

	public function setId(int $id = -1) {
		$this->id = $id;
	}

	public function setCreatedAt(string $created_at = '') {
		$this->created_at = $created_at;
	}

	public function setUpdatedAt(string $updated_at = '') {
		$this->updated_at = $updated_at;
	}

	public function setTitle(string $title = '') {
		$this->title = $title;
	}

	public function setDescription(string $description = '') {
		$this->description = $description;
	}

	public function setLimitDate(string $limit_date = null) {
		$this->limit_date = $limit_date;
	}

	public function setCardId(int $card_id = -1) {
		$this->card_id = $card_id;
	}

	public function setCreatorId(int $creator_id = -1) {
		$this->creator_id = $creator_id;
	}

	public function toJSON() : array {
		/*return array(
			'id' => $this->getId(),
			'created_at' => $this->getCreatedAt(),
			'updated_at' => $this->getUpdatedAt(),
			'title' => $this->getTitle(),
			'description' => $this->getDescription(),
			'limit_date' => $this->getLimitDate(),
			'card_id' => $this->getCardId(),
			'creator_id' => $this->getCreatorId()
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