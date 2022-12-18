<?php
//var_dump(__DIR__);
//var_dump(DIRECTORY_SEPARATOR);
/**
 * Script basé sur Routeur grafikart : https://www.youtube.com/watch?v=VvqkB80OSHU(nouveau) && https://www.youtube.com/watch?v=I-DN2C7Gs7A (ancien routeur) 
*/
/**
 * URL : http://localhost/Okanban/public/
 * 
 * Note :
 * Si php ne trouve pas une classe, c'est parce que il faut préciser l'extension .class, 
 * car l'autoloader utilise les fichiers portant le suffixe .class
 * 
*/
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'src', 'Autoloader.class.php'));
src\Autoloader::register();

use src\Router;
use src\RouterException;
use src\utility\UtilityFunction;

// define('CONTROLLER_PATH', join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'src', 'controller')));
define('VIEW_PATH', join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'src', 'view')));
define('ASSET_PATH', join(DIRECTORY_SEPARATOR, array('assets')));
define('AJAX_PATH', join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'src', 'ajax')));
define('DEBUG_TIME', microtime(true));
define('PROJECT_CREATION_YEAR', '2022');
define('PROJECT_NAME', 'Okanban');

$inputUrl = "";
if (isset($_SERVER['REQUEST_METHOD'])) {
	if (!empty($_SERVER['REQUEST_METHOD'])) {
		// Récupère $1 de l'htaccess : ./index.php?url=$1
		if (isset($_GET['url'])) {
			if (!empty($_GET['url'])) {
				$inputUrl = UtilityFunction::test_input($_GET['url']);
			}
		}
	}
}

$domain = "localhost";
$root = "Okanban/public";

/**
 * Suppression de la barre oblique de fin url si il y en a car sinon le css ne charge pas
 * ex : /accueil css chargé - /accueil/ css non chargé 
*/
if (($_SERVER['REQUEST_URI'] != '/') && preg_match('#/$#', $_SERVER['REQUEST_URI'])) {
	if (strcmp($_SERVER['REQUEST_URI'], "/{$root}/") != 0) {
		header('Location: ' . preg_replace('#/$#', '', $_SERVER['REQUEST_URI']));
		exit();
	}
}

$rootUrl = "http://{$domain}/${root}/";
$router = new Router($rootUrl, $inputUrl);

// Les routes pour requêtes ajax
/*----------------------------------------------------------------------------*/
/*                          ROUTES DES REQUÊTES HTTP AJAX                     */
/*----------------------------------------------------------------------------*/
$router->get('/kanban/ajax/:slug', function($slug) use ($router) {
	require join(DIRECTORY_SEPARATOR, array(AJAX_PATH, 'kanban.php'));
}, 'kanban')->with('slug', '[a-z\0-9!@&_+\-]+');

$router->post('/kanban/ajax/ajouter-colonne/:slug', function($slug) use ($router) {
	require join(DIRECTORY_SEPARATOR, array(AJAX_PATH, 'newcardkanban.php'));
}, 'newcardkanban')->with('slug', '[a-z\0-9!@&_+\-]+');

$router->post('/kanban/ajax/ajouter-tache/:slug', function($slug) use ($router) {
	require join(DIRECTORY_SEPARATOR, array(AJAX_PATH, 'newtaskkanban.php'));
}, 'newtaskkanban')->with('slug', '[a-z\0-9!@&_+\-]+');

$router->post('/kanban/ajax/maj-tache/:slug', function($slug) use ($router) {
	require join(DIRECTORY_SEPARATOR, array(AJAX_PATH, 'updatetaskkanban.php'));
}, 'newtaskkanban')->with('slug', '[a-z\0-9!@&_+\-]+');

$router->post('/kanban/ajax/inviter-utilisateur', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(AJAX_PATH, 'inviteuserkanban.php'));
}, 'inviteuserkanban');

/*----------------------------------------------------------------------------*/
/*                       ROUTES DES REQUÊTES HTTP BASIQUE                     */
/*----------------------------------------------------------------------------*/

// http://localhost/Okanban/public/kanban/mon-kanban-4
$router->get('/kanban/:slug', function($slug) use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'kanban.php'));
}, 'kanban')->with('slug', '[a-z\0-9!@&_+\-]+');

// http://localhost/Okanban/public/accueil
$router->get('/accueil', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'home.php'));
}, 'home');

// http://localhost/Okanban/public/mes-kanbans
$router->get('/mes-kanbans', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'kanbans.php'));
}, 'kanbans');

// http://localhost/Okanban/public/mes-participations-aux-kanbans
$router->get('/mes-participations-aux-kanbans', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'kanbansparticipations.php'));
}, 'kanbansparticipations');

// http://localhost/Okanban/public/mes-taches-pour-un-kanban
$router->get('/mes-taches-pour-un-kanban', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'kanbantasks.php'));
}, 'kanbantasks');

// http://localhost/Okanban/public/mes-taches-pour-tous-mes-kanban
$router->get('/mes-taches-pour-tous-mes-kanban', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'kanbanstasks.php'));
}, 'kanbanstasks');

// http://localhost/Okanban/public/nouveau-kanban
$router->get('/nouveau-kanban', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'newkanban.php'));
}, 'newkanban');

// http://localhost/Okanban/public/nouveau-kanban
$router->post('/nouveau-kanban', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'newkanban.php'));
}, 'newkanban');

// http://localhost/Okanban/public/apropos
$router->get('/apropos', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'about.php'));
}, 'about');

// http://localhost/Okanban/public/connexion
$router->get('/connexion', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'login.php'));
}, 'login');

// http://localhost/Okanban/public/connexion
$router->post('/connexion', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'login.php'));
}, 'login');

// http://localhost/Okanban/public/connexion
$router->get('/inscription', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'register.php'));
}, 'register');

// http://localhost/Okanban/public/inscription
$router->post('/inscription', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'register.php'));
}, 'register');

// http://localhost/Okanban/public/deconnexion
$router->get('/deconnexion', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'logout.php'));
}, 'logout');

// http://localhost/Okanban/public/erreur
$router->get('/erreur', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'error404.php'));
}, 'error404');

// http://localhost/Okanban/public/
$router->get('/', function() use ($router) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'home.php'));
}, 'default');

try {
	$router->run();
} catch (RouterException $re) {
	require join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'error404.php'));
}
unset($rootUrl);
unset($router);
unset($inputUrl);
?>
