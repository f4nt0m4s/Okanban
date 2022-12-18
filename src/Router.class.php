<?php
namespace src;

use src\Route;
use src\RouterException;

/**
	* Class Router
	* Gestion des routes de l'URL
*/
class Router {

	private $urlRoot; // Prefixe de l'url (pour les chemins absolues des urls)
	private $url; // Contiendra l'URL sur laquelle on souhaite se rendre
	private $routes = []; // Contiendra la liste des routes
	private $namedRoutes = [];

	public function __construct($urlRoot="", $url) {
		$this->urlRoot = $urlRoot;
		$this->url = $url;
	}

	private function add($path, $callable, $name, $method) : Route {
		$route = new Route($path, $callable);
		$this->routes[$method][] = $route;
		if (is_string($callable) && $name === null) {
			$name = $callable;
		}
		if ($name) {
			$this->namedRoutes[$name] = $route;
		}
		return $route;
	}

	public function get($path, $callable, $name = null) : Route {
		return $this->add($path, $callable, $name, 'GET');
	}

	public function post($path, $callable, $name = null) : Route {
		return $this->add($path, $callable, $name, 'POST');
	}

	public function run() {
		if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
			throw new RouterException('REQUEST_METHOD does not exist');
		}
		foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
			//var_dump($route);
			if ($route->match($this->url)) {
				return $route->call();
			}
		}
		throw new RouterException('No matching routes');
	}

	public function url($name, $params = []) : string {
		if (!isset($this->namedRoutes[$name])) {
			throw new RouterException('No route matches this name');
		}
		return $this->urlRoot . $this->namedRoutes[$name]->getUrl($params);
	}

	/**
		* Utilisation
		* $router = new Router($_GET['url']); 
		* $router->get('/', function($id){ echo "Bienvenue sur ma homepage !"; }, 'nomlienpage'); 
		* $router->get('/posts/:id', function($id){ echo "Voila l'article $id"; }, 'linkname');
		* $router->run(); 
	*/
}
?>