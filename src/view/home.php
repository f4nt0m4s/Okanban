<?php

use src\Session;
use src\exception\ForbiddenException;
use src\controller\KanbanController;

$user = null;
try {
	Session::checkSession();
} catch (ForbiddenException $e) {
}
$kanbanController = new KanbanController();
$kanbans = $kanbanController->getPublicKanbans();

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
		<title>Okanban | Accueil</title>
	</head>
	<body class="bg-light">
		<header>	
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'navbar.php')); ?>
		</header>
		<div class="container-fluid">
			<div class="d-flex justify-content-center row">
				<div class="col-lg-6 col-md-6 col-sm-12">
					<div class="container-fluid p-5">
						<h1 class="h1 display-4 text-center">Liste des modèles Kanban publique</h1>
<?php foreach ($kanbans as $k => $value) : ?>
						<div class="card mb-2">
							<div class="card-header">
								<p class="text-capitalize"><?= $value->getTitle() ?></p>
							</div>
							<div class="card-body">
							<p class="card-text"><?= $value->getTitle() ?></p>
							<a href="<?= $router->url('kanban', array('slug' => $value->getSlug())) ?>" class="btn btn-primary">Ouvrir ce kanban</a>
							</div>
							<?php
									$user = $kanbanController->getUserByCreatorId($value->getCreatorId()); 
									$username = !is_null($user) ? $user : 'Inconnu';
							?>
							<div class="card-footer">
								<small class="text-muted font-weight-bold">
									<?= ucfirst($username->getUsername()) . ", crée le " . date("d/m/Y à H:i ", strtotime($kanbans[$k]->getCreatedAt())) ?>
								</small>
							</div>
						</div>
<?php endforeach; ?>
					</div>
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