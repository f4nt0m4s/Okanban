<?php

use src\Session;
use src\exception\ForbiddenException;
use src\utility\UtilityFunction as UF;
use src\controller\KanbanController;
use src\controller\UserController;
use src\controller\KanbanUserController;

// Protection du fichier ajax - Source : https://stackoverflow.com/questions/33923260/getting-php-data-from-jquery-ajax-without-html-forms
$ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
	&& strcmp(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']), 'xmlhttprequest') == 0;
if (!$ajax) {
	header("Location: {$router->url('kanbans')}");
	exit();
}

if (isset($_POST['username'])) {
	if (!empty($_POST['username'])) {

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

		if (!isset($_POST['kanban'])) {
			$response['message'] = "Aucun kanban défini";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		$kanbanJSON = json_decode($_POST['kanban'], true);

		if (!isset($kanbanJSON['title'])) {
			$response['message'] = "Aucun kanban défini";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		$title = UF::test_input(htmlspecialchars_decode($kanbanJSON['title']));
		$usernameJSON =  UF::test_input(htmlspecialchars_decode($_POST['username']));
		
		$errorCode = json_last_error();
		if ($errorCode !== JSON_ERROR_NONE) {
			echo UF::jsonMessageError($errorCode);
			$response['message'] = "Erreur lors de l'anlyse du JSON {$errorCode}";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		$userController = new UserController();
		$user = $userController->getUserByUsername($usernameJSON);
		if (is_null($user)) {
			$response['message'] = "Aucun utilisateur pour {$usernameJSON}";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}
		$kanbanController = new KanbanController();
		$kanban = $kanbanController->getKanbanByTitle($title);
		if (is_null($kanban)) {
			$response['message'] = "Aucun kanban existant pour {$title} inexistant";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		if ($kanban->getCreatorId() == $user->getId()) {
			$response['message'] = "En tant que créateur du kanban, vous ne pouvez pas vous ajouter vous même en invité";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		$kanbanUserController = new KanbanUserController();
		if (!is_null($kanbanUserController->getKanbanUser($kanban, $user))) {
			$response['message'] = "L'utilisateur {$user->getUsername()} fait déja partie du kanban";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		if (!$kanbanUserController->addUserToKanban($kanban, $user)) {
			$response['message'] = "Echec de l'invitation de {$user->getUsername()} au kanban {$kanbanJSON['title']}";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		$response['valid'] = true;
		$response['message'] = $user->getUsername() . "a été invité";
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}
}


?>