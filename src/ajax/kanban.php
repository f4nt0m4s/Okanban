<?php

/**
 * Requête GET AJAX
 * Récupère le kanban avec ses colonnes et tâches
 * La variable $slug prvient du router ($router->get('/kanban/ajax/:slug', function($slug))
*/


// Protection du fichier ajax - Source : https://stackoverflow.com/questions/33923260/getting-php-data-from-jquery-ajax-without-html-forms
$ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
	&& strcmp(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']), 'xmlhttprequest') == 0;
if (!$ajax) {
	header("Location: {$router->url('kanbans')}");
	exit();
}

// Vérification de l'en tête origine afin de savoir si c'est le serveur officiel
// Source : https://stackoverflow.com/questions/37912937/how-to-send-secure-ajax-requests-with-php-and-jquery
/*
header('Content-Type: application/json');
if (isset($_SERVER['HTTP_ORIGIN'])) {
	$address = 'http://' . $_SERVER['SERVER_NAME'];
	if (strpos($address, $_SERVER['HTTP_ORIGIN']) !== 0) {
		exit(json_encode([
			'error' => 'Invalid Origin header: ' . $_SERVER['HTTP_ORIGIN']
		]));
	}
} else {
	exit(json_encode([
		'error' => 'No Origin header'
	]));
}
*/

use src\controller\KanbanController;
use src\controller\CardController;
use src\controller\TaskController;
use src\controller\UserController;

if (!isset($slug) || !is_string($slug)) {
	exit();
}

$response = [];

$kanbanController = new KanbanController();
$kanban = $kanbanController->getKanbanBySlug($slug);
if (is_null($kanban)) {
	// renvoie une erreur json
	exit();
}
$response['kanban']['kanban'] = $kanban->toJSON();

// Récupère le kanban à partir du slug fournit en url (http://localhost/Okanban/public/kanban/opi-19xtf1uf)
// opi-19xtf1uf = slug
$cardController = new CardController();
$cards = $cardController->getCardsByKanbanId($kanban->getId());
$response['kanban']['cards'] = array(); 
foreach ($cards as $card) {
	array_push($response['kanban']['cards'], $card->toJSON());
}

$taskController = new TaskController();
$userController = new UserController();
for ($i = 0; $i < count($cards); $i++) {
	$card = $cards[$i];
	$tasks = $taskController->getTasksByCardId($card->getId());
	$response['kanban']['cards'][$i]['tasks'] = array();
	foreach ($tasks as $task) {
		// participants à une tâche
		$users = $userController->getUsersByTaskId($task->getId());
		$usernames = array();
		foreach ($users as $user) {
			array_push($usernames, $user->getUsername());
		}
		$objTask = (object) array('task' => $task->toJSON(), 'usernames' => $usernames);
		array_push($response['kanban']['cards'][$i]['tasks'], $objTask);
	}
}

$users = $userController->getUsersByKanbanId($kanban->getId());
$response['kanban']['usernames'] = array();
foreach ($users as $user) {
	array_push($response['kanban']['usernames'], $user->getUsername());
}

header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE);

?>