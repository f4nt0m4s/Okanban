<?php
namespace src\model;

class Kanban {

	private $id;
	private $created_at;
	private $title;
	private $description;
	private $slug;
	private $visibility;
	private $creator_id;

	private static $visiblities = [
		0 => ['id' => 0, 'value' => 'private', 'content' => 'privée'],
		1 => ['id' => 1, 'value' => 'public', 'content' => 'publique']
	];

	public function __construct(int $id = -1, string $created_at = '', string $title = '', string $description = '', string $slug = '', int $visibility = 0, int $creator_id = -1) {
		$this->id = $id;
		$this->created_at = $created_at;
		$this->title = $title;
		$this->description = $description;
		$this->slug = $slug;
		$this->visibility = $visibility;
		$this->creator_id = $creator_id;
	}

	public function __destruct() {
		$this->id = null;
		$this->created_at = null;
		$this->title = null;
		$this->description = null;
		$this->slug = null;
		$this->visibility = null;
		$this->creator_id = null;
		unset($this->id);
		unset($this->created_at);
		unset($this->title);
		unset($this->description);
		unset($this->slug);
		unset($this->visibility);
		unset($this->creator_id);
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

	public function getDescription() : string {
		return $this->description;
	}

	public function getSlug() : string {
		return $this->slug;
	}

	public function getVisiblity() : int {
		return $this->visibility;
	}

	public static function getVisibilityPrivate() : int {
		return self::$visiblities[0]['id'];
	}

	public static function getVisibilityPublic() : int {
		return self::$visiblities[1]['id'];
	}

	public static function getVisibilityByValue(string $value) : int {
		if (strcmp($value, self::$visiblities[0]['value']) == 0) {
			return self::getVisibilityPrivate();
		} else if (strcmp($value, self::$visiblities[1]['value']) == 0) {
			return self::getVisibilityPublic();
		}
		return self::getVisibilityPrivate();
	}

	public static function getAllVisibility() : array {
		return self::$visiblities;
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

	public function setTitle(string $title) {
		$this->title = $title;
	}

	public function setDescription(string $description = '') {
		$this->description = $description;
	}

	public function setSlug(string $slug = '') {
		$this->slug = $slug;
	}

	public function setVisibility(int $visibility = 0) {
		$this->visibility = $visibility;
	}

	public function setCreatorId(int $creator_id = -1) {
		$this->creator_id = $creator_id;
	}

	public function toJSON() : array {
		/*return array(
			'id' => $this->getId(),
			'created_at' => $this->getCreatedAt(),
			'title' => $this->getTitle(),
			'description' => $this->getDescription(),
			'slug' => $this->getSlug(),
			'visibility' => $this->getVisiblity(),
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