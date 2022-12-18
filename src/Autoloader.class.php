<?php
namespace src;
/**
	* Le fichier importé avec "use" doit avoir le format file.class.php
	* Autoloader Permet d'éviter de require/include 'nomclasse' à chaque fois qu'il y a besoin de classe
*/
class Autoloader
{
	/**
		* Enregistre notre autoloader
	*/
	static function register() {
		spl_autoload_register(array(__CLASS__, 'autoload'));
    }

	/**
    	* Inclut le fichier correspondant à notre classe
		* @param $class string Le nom de la classe à charger
	*/
    static function autoload($class) {
		//var_dump(__DIR__); /// dans le dossier class
		//var_dump($class);
		if (strpos($class, __NAMESPACE__ . '\\') === 0) {
			$class = str_replace(__NAMESPACE__ . '\\', '', $class);
			$class = str_replace('\\', '/', $class);
			if (file_exists(__DIR__ . '/' . $class . '.class.php')) {
				require __DIR__ . DIRECTORY_SEPARATOR . $class . '.class.php';
			}
		}
    }
	/**
		* Utilisation
		* require 'Autoloader.php'; 
		* Autoloader::register();
	*/
}
?>