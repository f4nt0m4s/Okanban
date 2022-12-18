<?php

use src\Session;
use src\exception\ForbiddenException;
use src\utility\UtilityFunction as UF;
use src\controller\KanbanController;
use src\controller\CardController;
use src\model\Card;

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

		// Récupère le kanban
		$kanbanController = new KanbanController();
		$kanban = $kanbanController->getKanbanBySlug($slug);
		if (is_null($kanban)) {
			$response['message'] = "Aucun kanban trouvé";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		$title = isset($data['title']) ? UF::test_input(htmlspecialchars_decode($data['title'])) : "";
		/*
		// Désactivation de cette contrainte, l'utilisateur peut nommer des cartes identiques
		// Vérifie que la colonne n'existe pas déja dans ce kanban
		$card = $cardController->getCardByTitleKanbanId($title, $kanban->getId());
		if (!is_null($card)) {
			$response['message'] = "La carte ${title} existe déja";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}
		*/

		$date = date("Y-m-d H:i:s");
		$card = new Card(-1, $date, $title, $kanban->getId());
		$cardController = new CardController();
		$cardInserted = $cardController->createCard($card);
		if (is_null($cardInserted)) {
			$response['message'] = "La colonne {$title} n'a pas été crée";
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			exit();
		}

		$response['valid'] = true;
		$response['message'] = "La colonne {$title} a été crée";
		$response['card'] = $cardInserted->toJSON();
		echo json_encode($response, JSON_UNESCAPED_UNICODE);

	}
}

?>