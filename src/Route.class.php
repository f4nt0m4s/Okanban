<?php
namespace src;
/**
	* Class Route
	* Création d'une route
*/
class Route {

	private $path;
	private $callable;
	private $matches = [];
	private $params = [];

	public function __construct($path, $callable) {
		$this->path = trim($path, '/'); // Retire les / inutiles
		$this->callable = $callable;
	}

	/**
		* Permettra de capturer l'url avec les paramètre 
		* get('/kanban/:slug-:id') par exemple
	*/
	public function match($url) : bool {
		$url = trim($url, '/');
		$path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
		$regex = "#^$path$#i";
		if (!preg_match($regex, $url, $matches)) {
			return false;
		}
		array_shift($matches);
		$this->matches = $matches;
		return true;
	}

	private function paramMatch($match) : string {
		if (isset($this->params[$match[1]])) {
			return '(' . $this->params[$match[1]] . ')';
		}
		return '([^/]+)';
	}

	public function call() {
		// Vérifie que $this->callable est une chaîne de caractères
			// ex : $router->get('/article/:id', "post#insert" );
		if (is_string($this->callable)) {
			$params = explode('#', $this->callable);
			// params[0] = nom du controllers ex : KanbanControllers
			$controller = "src\controller\\" . $params[0] . "Controller"; // Donc ici ca va donner : "src\controller\KanbanController"
			$controller = new $controller();
			return call_user_func_array([$controller, $params[1]], $this->matches);
		} else {
			// Sinon, c'est une fonction ex : $router->get('/article/:id', function(){ require __DIR__ .'/home.php'; } ); 	
			return call_user_func_array($this->callable, $this->matches);
		}
	}

	public function with($param, $regex) : Route {
		$this->params[$param] = str_replace('(', '(?:', $regex);
		return $this; // On retourne tjrs l'objet pour enchainer les arguments
	}

	/**
	 * ex : $path = forum/:slug
	 * $param = array('slug' => 'valeur')
	*/
	public function getUrl($params) : string {
		$path = $this->path;
		foreach ($params as $k => $v) {
			$path = str_replace(":$k", $v, $path);
		}
		return $path;
	}
}
?>