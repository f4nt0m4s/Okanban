<?php
namespace src\form\validation;

class ValidationError {

	private $key;
	private $rule;
	private $attributes;
	
	private $message = [
		'required' => 'Le champ %s est requis',
		'empty' => 'Le champ %s ne peut être vide',
		'slug' => 'Le champ %s n\'est un slug valide',
		'minLength' => 'Le champ %s doit contenir au moins %d caractères',
		'maxLength' => 'Le champ %s doit contenir au plus %d caractères',
		'betweenLength' => 'Le champ %s doit contenir entre %d et %d caractères',
		'datetime' => 'Le champ %s doit être une date valide (%s)',
		'email' => 'L\'entrée %s doit être une adresse email valide',
		'notEquals' => 'Le champ %s n\'est pas identique au champ %s',
		'minNumber' => 'Le champ %s doit contenir au moins %d chiffres',
		'minLowerCase' => 'Le champ %s doit contenir au moins %d lettres minuscules',
		'minUpperCase' => 'Le champ %s doit contenir au moins %d lettres majuscules',
		'minSpecialChar' => 'Le champ %s doit contenir au moins %d caractères speciaux',
		'intNumber' => 'Le champ %s doit contenir une valeur entière'
	];

	public function __construct(string $key, string $rule, array $attributes = []) {
		$this->key = $key;
		$this->rule = $rule;
		$this->attributes = $attributes;
	}

	public function __destruct() {
		$this->key = null;
		$this->rule = null;
		unset($this->key);
		unset($this->rule);
	}

	public function __toString() : string {
		// $this->rules = 'required'
		// $this->key = 'content'
		$params = array_merge([$this->message[$this->rule], $this->key], $this->attributes);
		// call user function array pour apeller la fonction sprintf pour passer un tableau
		return (string) call_user_func_array('sprintf', $params);
	}
}

?>