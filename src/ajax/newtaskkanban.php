<?php

use src\Session;
use src\exception\ForbiddenException;
use src\utility\UtilityFunction as UF;
use src\controller\KanbanController;
use src\controller\CardController;
use src\controller\TaskController;
use src\controller\TaskUserController;
use src\controller\UserController;
use src\model\Task;

// Protection du fichier ajax - Source : https://stackoverflow.com/questions/33923260/getting-php-data-from-jquery-ajax-without-html-forms
$ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
	&& strcmp(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']), 'xmlhttprequest') == 0;
if (!$ajax) {
	header("Location: {$router->url('kanbans')}");
	exit();
}

if (isset($_POST['data'])) {
	if (!empty($_POST['data'])) {
		$data = json_decode($_POST['data'], true);
		$errorCode = json_last_error();
		if ($errorCode !== JSON_ERROR_NONE) {
			echo UF::jsonMessageError($errorCode);
			exit();
		}

		header('Content-Type: application/json');

		$response['connected'] = false;
		$response['valid'] = false;
		$response['message'] = '';

		try {
			Session::checkSession();
			$response['connected'] = true;
		} catch (ForbiddenException $e) {
			$response['connected'] = false;
			$response['message'] = "Veuillez-vous connecter";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		$userController = new UserController();
		$kanbanController = new KanbanController();
		
		// Vérifie que le kanban correspond
		$kanban = $kanbanController->getKanbanBySlug($slug);
		if (is_null($kanban)) {
			$response['message'] = "Aucun kanban trouvé";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		// Vérifie que la colonne correspond
		if (!isset($data['card']['id']) && !isset($data['card']['title'])) {
			$response['message'] = "Aucune colonne trouvé";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}
		$card_id = intval(UF::test_input($data['card']['id']));
		$card_title = UF::test_input($data['card']['title']);
		$cardController = new CardController();
		$card = $cardController->getCardByTitleKanbanId($card_title, $kanban->getId());
		if (is_null($card)) { 
			$response['message'] = "Aucune colonne trouvé";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			echo $card_title;
			echo $kanban->getId();
			exit();
		}
		if ($card_id != $card->getId()) {
			$response['message'] = "Aucune colonne trouvé";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		$usernames = $data['usernames'];
		if (!is_array($usernames)) {
			$response['message'] = "La liste d'utilisateur est incorrect (elle n'est pas un tableau JS)";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}
		foreach ($usernames as $u => $v) {
			$usernames[$u] = UF::test_input($v);
		}

		$users = $userController->getUsersByKanbanId($kanban->getId());
		if (empty($users)) {
			$response['message'] = "Aucun utilisateur trouvé pour ce kanban";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}
		$usersUsernames = array();
		foreach ($users as $user) { array_push($usersUsernames, $user->getUsername()); }
		foreach ($usernames as $username) {
			if (!in_array($username, $usersUsernames)) {
				$response['message'] = "L'utilisateur {$username} ne fait pas parti de ce kanban";
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				exit();
			}
		}

		if (!isset($data['title']) || empty($data['title'])) {
			$response['message'] = "La titre de la tâche ne peut être vide";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		$title = UF::test_input($data['title']);
		$description = isset($data['description']) ? UF::test_input($data['description']) : "";
		$limit_date = '';
		if (isset($data['limitdate']) && !empty($data['limitdate'])) {
			$timestamp = strtotime(UF::test_input($data['limitdate']));
			if (is_int($timestamp)) {
				$limit_date = date('Y-m-d H:i', $timestamp);
			}
		}

		$date = date("Y-m-d H:i:s");
		$taskController = new TaskController();
		$task = new Task(-1,
				$date,
				$date,
				$title,
				$description,
				$limit_date,
				$card_id,
				$user->getId()
			);
		$task = $taskController->createTask($task);
		if (is_null($task)) {
			$response['message'] = "La tâche n'a pas été crée";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		// Ajout des participants à la tâche
		$taskUserController = new TaskUserController();
		$users = array();
		$user = null;
		foreach ($usernames as $username) {
			if (!is_null($user = $userController->getUserByUsername($username))) {
				array_push($users, $user);
			}
		}
		foreach ($users as $user) {
			if (!$taskUserController->addUserToTask($task, $user)) {
				$key = "";
				if (($key = array_search($user->getUsername(), $usersUsernames))) {
					unset($usernames[$key]);
				}
				$response['message'] = "L'utilisateur {$user->getUsername()} n'a pas été ajouté";
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				exit();
			}
		}

		$response['valid'] = true;
		$response['message'] = "La tâche {$title} a été crée";
		$objTask = (object) array('task' => $task->toJSON(), 'usernames' => $usernames);
		$response['task'] = $objTask;

		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}
}

?>