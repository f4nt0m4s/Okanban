<?php

use src\Session;
use src\exception\ForbiddenException;
try {
	Session::checkSession();
} catch (ForbiddenException $e) {
	Session::destroy();
	header('Location: ' . $router->url('home'));
	exit();
}

use src\utility\UtilityFunction as UF;
use src\URLUtility;
use src\controller\KanbanController;
use src\controller\form\NewKanbanFormController;
use src\model\Kanban;
use src\form\Form;

$validatorError = [];
$error = [];
$data = [];
$key = NewKanbanFormController::getKeyForm();
$visibility = KanbanController::getAllVisibility();
foreach ($key as $k => $value) {
	$data[$k] = '';
}

if (isset($_POST)) {
	if (!empty($_POST)) {
		foreach ($key as $k => $v) {
			$data[$k] = UF::test_input($_POST[$v]);
		}
		$newKanbanFormController = new NewKanbanFormController($data);
		$validatorError = $newKanbanFormController->getValidatorError();
		if ($newKanbanFormController->isValid()) {

			Session::start();
			$userSession = unserialize($_SESSION[Session::getUserIndex()]);
			if (is_null($userSession) || !$userSession ) {
				Session::destroy();
				header('Location: ' . $router->url('home'));
				exit();
			}
			$slug = URLUtility::slugify($data[NewKanbanFormController::getKeyFormTitle()]) . '-' . URLUtility::shortHash($userSession->getId());
			$date = date("Y-m-d H:i:s");
			$kanban = new Kanban(
				-1,
				$date,
				$data[NewKanbanFormController::getKeyFormTitle()],
				$data[NewKanbanFormController::getKeyFormDescription()],
				$slug,
				Kanban::getVisibilityByValue($data[NewKanbanFormController::getKeyFormVisibility()]),
				$userSession->getId()
			);

			$kanbanController = new KanbanController();
			if ($kanbanController->createKanban($kanban)) {
				header('Location: ' . $router->url('kanbans'));
				exit();
			} else {
				$error[$k] = 'Ce kanban est déja existant ou erreur base de données lors de la création du kanban';
			}

		} else {
			$validatorError = $newKanbanFormController->getValidatorError();
		}
		foreach($validatorError as $k => $validationError) {
			$error[$k] = $validationError->__toString();
		}
	}
}
// Nombre de colonnes par défault pour le formulaire
$data[$key[NewKanbanFormController::getKeyFormNumberColumns()]] = KanbanController::getDefaultNbColumns();
$form = new Form($data, $error);

?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="stylesheet" type="text/css" href="<?= join(DIRECTORY_SEPARATOR, array(ASSET_PATH, 'bootstrap-4.5.3-dist', 'css', 'bootstrap.min.css')) ?>">
		<link rel="stylesheet" type="text/css" href="<?= join(DIRECTORY_SEPARATOR, array(ASSET_PATH, 'css', 'dark-mode.css')) ?>">
		<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'favicon.php')); ?>
		<title>Okanban | Création</title>
	</head>
	<body class="bg-light">
		<header>	
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'navbar.php')); ?>
		</header>
		<div class="container-fluid p-5">
			<div class="row justify-content-center">
				<div class="col-lg-6 col-md-8 order-md-first order-sm-first">
					<form action="" method="post" onsubmit="this.submit(); this.reset(); return false;">
						<h2 class="h2 display-4 text-center">Nouveau Kanban</h2>
						<div class="form-group">
							<?= $form->input('text', $key[NewKanbanFormController::getKeyFormTitle()], 'Titre du Kanban', 'Entrer un titre', $data[$key[NewKanbanFormController::getKeyFormTitle()]]); ?>
							<small id="kanbanTitleInfo" class="form-text text-muted">Un titre pour votre modèle kanban</small>
						</div>
						<div class="form-group">
							<?= $form->textarea($key[NewKanbanFormController::getKeyFormDescription()], 'Description du Kanban', 'Entrer une description', $data[$key[NewKanbanFormController::getKeyFormDescription()]]); ?>
						</div>
						<div class="form-group">
							<?= $form->input('number', $key[NewKanbanFormController::getKeyFormNumberColumns()], 'Nombre de colonnes', 'Entrer le nombre de colonnes', $data[$key[NewKanbanFormController::getKeyFormNumberColumns()]]); ?>
							<small id="numberColumnsInfo" class="form-text text-muted">Le nombre de colonnes par défault est de <?= KanbanController::getDefaultNbColumns(); ?>.</small>
						</div>
						<div class="form-group">
							<?php
								for ($i = 0; $i < count($visibility); $i++) {
									if (isset($visibility[$i]['content'])) {
										$visibility[$i]['content'] = ucfirst($visibility[$i]['content']);
									}
								}
								echo $form->select($key[NewKanbanFormController::getKeyFormVisibility()], 'Visibilité', $visibility);
							?>
						</div>
						<div class="form-group text-center">
							<button type="submit" class="btn btn-primary">Créer</button>
						</div>
					</form>
				</div>
			</div>
		</div>
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'footer.php')); ?>
		<noscript><p>Cette application nécessite l'activation de JavaScript.</p></noscript>
		<script type="text/javascript" src="<?= join(DIRECTORY_SEPARATOR, array(ASSET_PATH, 'bootstrap-4.5.3-dist', 'jquery-3.2.1.min.js')) ?>"></script>
		<script type="text/javascript" src="<?= join(DIRECTORY_SEPARATOR, array(ASSET_PATH, 'bootstrap-4.5.3-dist', 'js', 'bootstrap.bundle.min.js')) ?>"></script>
		<script type="text/javascript" src="<?= join(DIRECTORY_SEPARATOR, array(ASSET_PATH, 'js', 'dark-mode-switch.js')) ?>"></script>
	</body>
</html>
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'loadingtime.php')); ?>