<?php

use src\controller\TaskController;
use src\Session;
use src\exception\ForbiddenException;
use src\controller\KanbanController;
use src\controller\CardController;
use src\utility\UtilityFunction as UF;

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

		// Vérification que le kanban correspond
		$kanbanController = new KanbanController();
		$kanban = $kanbanController->getKanbanBySlug($slug);
		if (is_null($kanban)) {
			$response['message'] = "Aucun kanban trouvé slug={$slug}";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		$taskTitle = UF::test_input($data["taskTitle"]);
		$cardTitleSource = UF::test_input($data["cardTitleSource"]);
		$cardTitleDestination = UF::test_input($data["cardTitleDestination"]);

		$cardController = new CardController();
		$cardSource = $cardController->getCardByTitleKanbanId($cardTitleSource, $kanban->getId());
		if (is_null($cardSource)) {
			$response['message'] = "Aucune colonne {$cardTitleSource} source trouvé";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}
		$cardDestination = $cardController->getCardByTitleKanbanId($cardTitleDestination, $kanban->getId());
		if (is_null($cardDestination)) {
			$response['message'] = "Aucune colonne {$cardTitleDestination} destination trouvé";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		// Récupère la tâche associé à la colonne source
		$taskController = new TaskController();
		$task = $taskController->getTaskByTitleCardId($taskTitle, $cardSource->getId());
		if (is_null($task)) {
			$response['message'] = "Aucune tâche {$taskTitle} trouvé pour la colonne {$cardTitleSource}";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		if (!$taskController->updateCardIdById($cardDestination->getId(), $task->getId())) {
			$response['message'] = "La tâche {$taskTitle} n'a pas pu être mis à jour vers {$cardTitleDestination}";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		$response['valid'] = true;
		$response['message'] = '';
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}
}


?>