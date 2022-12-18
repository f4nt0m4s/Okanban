<?php 
namespace src\form;

class Form {

	private $data;
	private $error;

	public function __construct($data = [], $error = []) {
		$this->data = $data;
		$this->error = $error;
	}

	public function __destruct() {
		$this->data = null;
		$this->error = null;
		unset($this->data);
		unset($this->error);
	}

	/**
	 * @param $type : Le type de l'input (text, email, password, submit)
	 * @param $key : La clé de l'input
	 * @param $label : Le nom de l'input
	 * @param $placeholder
	 */
	public function input(string $type, string $key, string $label, string $placeholder, string $value="") : string {
		// syntax heredoc : https://www.php.net/manual/fr/language.types.string.php#language.types.string.syntax.heredoc
		return <<<HTML
				<label for="{$key}">{$label}</label>
				<input type="{$type}" id="{$key}" name="{$key}" class="form-control" value="{$value}" placeholder="{$placeholder}" required>
				{$this->getErrorFeedback($key)}
HTML;
	}
	
	public function textarea(string $key, string $label, string $placeholder, $value="") : string {
		return <<<HTML
			<label for="{$key}">{$label}</label>
			<textarea id="{$key}" name="{$key}" placeholder="{$placeholder}" class="form-control" required>$value</textarea>
			{$this->getErrorFeedback($key)}
HTML;
	}

	/**
	 * options[0] = ['value' => 'valeur1', 'content' => 'contenu de l'option1', 'selected' => true]
	 * options[1] = ['value' => 'valeur2', 'content' => 'contenu de l'option2', 'selected' => false]
	 */
	public function select(string $key, string $label, array $option=[]) : string {
		$str = "<label for=\"{$key}\">{$label}</label>\n";
		$str .= "<select id=\"{$key}\" name=\"{$key}\" class=\"mr-sm-2 custom-select\" data-style=\"btn-primary\">\n";
		$len = count($option);
		for ($i = 0; $i < $len; $i++) {
			$value = isset($option[$i]['value']) ? $option[$i]['value'] : '';
			$content = isset($option[$i]['content']) ? $option[$i]['content'] : '';
			$selected = isset($option[$i]['selected']) ? $option[$i]['selected'] ? 'selected' : '' : '';
			$str .= "\t<option value=\"{$value}\" {$selected}>{$content}</option>\n";
		}
		$str .= "</select>\n";
		$str .= $this->getErrorFeedback($key);
		return $str;
	}

	public function option(string $value='', string $content = '', bool $selected=false) : array {
		return ['value' => $value, 'content' => $content, 'selected' => $selected];
	}

	/**
	 * d-block car sinon invalid-feedback reste en display none pour la version bootstrap 4
	*/
	private function getErrorFeedback(string $key) : string {
		if (isset($this->error[$key])) {
			$error = '';
			if (is_array($this->error[$key])) {
				$error = implode('<br>', $this->error[$key]); 
			} else {
				$error = $this->error[$key];
			}
			return <<<HTML
			<div class="invalid-feedback d-block">
				<p class="m-0 p-0">$error</p>
			</div>
HTML;
		}
		return '';
	}
}
?>