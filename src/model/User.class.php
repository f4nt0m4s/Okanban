<?php
namespace src\model;

class User {

	private $id;
	private $created_at;
	private $email;
	private $username;
	private $password;

	public function __construct(int $id = -1, string $created_at = '', string $email = '', string $username = '', string $password = '') {
		$this->id = $id;
		$this->created_at = empty($created_at) ? date("Y-m-d H:i:s") : $created_at;
		$this->email = $email;
		$this->username = $username;
		$this->password = $password;
	}

	public function __destruct() {
		$this->id = null;
		$this->created_at = null;
		$this->email = null;
		$this->username = null;
		$this->password = null;
		unset($this->id);
		unset($this->created_at);
		unset($this->email);
		unset($this->username);
		unset($this->password);
	}

	public function getId() : int {
		return $this->id;
	}

	public function getCreatedAt() : string {
		return $this->created_at;
	}

	public function getEmail() : string {
		return $this->email;
	}

	public function getUsername() : string {
		return $this->username;
	}

	public function getPassword() : string {
		return $this->password;
	}

	public function setId(int $id = -1) {
		$this->id = $id;
	}

	public function setCreatedAt(string $createdAt = '') {
		$this->created_at = $createdAt;
	}

	public function setEmail(string $email = '') {
		$this->email = $email;
	}

	public function setUsername(string $username = '') {
		$this->username = $username;
	}

	public function setPassword(string $password = '') {
		$this->password = $password;
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