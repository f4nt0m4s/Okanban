<?php
namespace src\database;

/**
 * Classe pour lire les var d'env
 * source : https://dev.to/fadymr/php-create-your-own-php-dotenv-3k2i
*/
class DotEnv {
	
	private $path;
	private $separator = '=';

	public function __construct($path) {
		if (!file_exists($path)) {
			throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
		}
		$this->path = $path;
	}

	/**
	 * Chargement du fichier de configuration
	 * @throws \RuntimeException 
	 * @return void
	 */
	public function load() : void {
		if (!is_readable($this->path)) {
			throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
		}
		$lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach($lines as $line) {
			// comments
			if (strpos(trim($line), '#') === 0) {
				continue;
			}
			list($name, $value) = explode($this->separator, $line, 2);
			$name = trim($name);
			$value = trim($value);
			if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
				putenv(sprintf('%s%s%s', $name, $this->separator, $value));
				$_ENV[$name] = $value;
				$_SERVER[$name] = $value;
			}
		}
	}
}

?>