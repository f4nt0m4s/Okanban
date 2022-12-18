<?php 
namespace src\form\validation;

use src\form\validation\ValidationError;
use \DateTime;

class Validator {

	private $params;
	private $errors;

	public function __construct($params = []) {
		$this->params = $params;
		$this->errors = [];
	}

	public function __destruct() {
		$this->params = null;
		$this->errors = null;
		unset($this->params);
		unset($this->errors);
	}
	
	public function isValid() : bool {
		return empty($this->errors);
	}

	/**
	 * Récupère les erreurs
	 * @return ValidationError[]
	 */
	public function getErrors() : array {
		return $this->errors;
	}
	
	/**
	 * Ajoute une erreur
	 * @param string $key
	 * @param string $rule
	 * @param array $attributes
	 * 
	 * @return void
	 */
	private function addError(string $key, string $rule, array $attributes = []) : void {
		$this->errors[$key] = new ValidationError($key, $rule, $attributes);
	}

	private function getValue(array $arr, string $key) {
		if (array_key_exists($key, $arr)) {
			return $arr[$key];
		}
		return null;
	}

	/**
	 * Vérifie que les champs sont requis dans le tableau
	 * Example : required('name1', 'name2', 'name3', ...)
	 * @param string ...$keys
	 * @return self
	 */
	public function required(string ...$keys) : self {
		foreach ($keys as $key) {
			$value = $this->getValue($this->params, $key);
			if (is_null($value)) {
				// $this->errors[$key] = "Le champ {$key} est vide";
				$this->addError($key, 'required');
			}
		}
		return $this;
	}

	/**
	 * Vérifie que le champ n'est pas vide
	 * Example : notEmpty('name1', 'name2', 'name3', ...);
	 * @param string ...$keys
	 * @return self
	 */
	public function notEmpty(string ...$keys) : self {
		foreach ($keys as $key) {
			$value = $this->getValue($this->params, $key);
			if (is_null($value) || empty($value)) {
				$this->addError($key, 'empty');
			}
		}
		return $this;
	}

	/**
	 * Vérifie que l'élément est un slug
	 * Un Slug (ou Friendly URL) est la partie d'une URL qui identifie le titre d'un article, un blog ou d'une news
	 * format : aaaa(1)-aaaa(2)-aaaa(3) (lettre minuscule avec (chiffre optionnel) séparé par un tiret)
	 * @param string $key
	 * @return Validator
	 */
	public function slug(string $key) : self {
		$value = $this->getValue($this->params, $key);
		$pattern = '/^([a-z0-9]+-?)+$/';
		if (!is_null($value) && !preg_match($pattern, $value)) {
			$this->addError($key, 'slug');
		}
		return $this;
	}

	// ?min veut dire que c'est pas forcément définie
	public function length(string $key, ?int $min, ?int $max = null) : self {
		$value = $this->getValue($this->params, $key);
		// il faut utiliser mb_strlen pour la sécurité mais je n'arrive pas à installer le module mb_string : mb_strlen($value, 'UTF-8');
		$length = strlen(utf8_decode($value));
		if (!is_null($min) && !is_null($max) && ($length < $min || $length > $max)) {
			$this->addError($key, 'betweenLength', [$min, $max]);
			return $this;
		}
		if (!is_null($min) && $length < $min) {
			$this->addError($key, 'minLength', [$min]);
			return $this;
		}
		if (!is_null($max) && $length > $max) {
			$this->addError($key, 'maxLength', [$max]);
		}
		return $this;
	}

	public function dateTime(string $key, string $format = 'Y-m-d H:i:s') : self {
		$value = $this->getValue($this->params, $key);
		$date = DateTime::createFromFormat($format, $value);
		$errors = DateTime::getLastErrors();
		if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
			$this->addError($key, 'datetime', [$format]);
		}
		return $this;
	}

	/**
	 * Vérifie que l'adresse email est correct
	 */
	public function email(string $key) : self {
		$value = $this->getValue($this->params, $key);
		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
			$this->addError($key, 'email', []);
		}
		return $this;
	}

	/**
	 * Vérifie que le mot de passe est correct
	*/
	public function password(string $key, $minLength=8, $maxLenth=16, $minNumberPassword=1, $minLetterLowerCase=1, $minLetterUpperCase=1, $minSpecialChars=1) : self {
		$value = $this->getValue($this->params, $key);
		if (strlen($value) < $minLength) {
			$this->addError($key, 'minLength', [$minLength]);
		} else if (!preg_match("#[0-9]{{$minNumberPassword}}#", $value)) {
			$this->addError($key, 'minNumber', [$minNumberPassword]);
		} else if (!preg_match("#[a-z]{{$minLetterLowerCase}}#", $value)) {
			$this->addError($key, 'minLowerCase', [$minLetterLowerCase]);
		} else if (!preg_match("#[A-Z]{{$minLetterUpperCase}}#", $value)) {
			$this->addError($key, 'minUpperCase', [$minLetterUpperCase]);
		} else if (!preg_match("#[\W]{{$minSpecialChars}}#", $value)) {
			$this->addError($key, 'minSpecialChar', [$minSpecialChars]);
		}
		return $this;
	}

	public function cmpPassword(string $key1, string $key2) : self {
		$pwd1 = $this->getValue($this->params, $key1);
		$pwd2 = $this->getValue($this->params, $key2);
		if (strcmp($pwd1, $pwd2) !== 0) {
			$this->addError($key2, 'notEquals', [$key1]);
		}
		return $this;
	}

	public function number(string $key) : self {
		$value = $this->getValue($this->params, $key);
		if (!ctype_digit($value)) {
			$this->addError($key, 'intNumber');
		}
		/*if (!filter_var(FILTER_VALIDATE_INT, $value)) {
			$this->addError($key, 'intNumber');
		}*/
		return $this;
	}
}
/**
 * Utilisation :
 * Dans la classe apellante :
 * $validator = $this->getValidator($request);
 * if ($validator->isValid()) {
 *    // success code
 * }
 * $errors = $validator->getErrors()
 * 
 * // Ecrire cette méthode dans la classe apellante
 * private function getValidator(string $request) {
 * 		return new Validator($request)
 * 			->required('content', 'name', 'slug')
 * 			->length('content', 10)
 * 			->length('name', 5, 250)
 * 			->length('slug', 2, 50)
 * 			->slug('slug');
 * } 
*/
?>