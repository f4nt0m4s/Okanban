<?php
use src\Session;
use src\exception\ForbiddenException;
use src\model\Kanban;
use src\controller\KanbanController;
// Vérification que le slug est correct Car un utilisateur peut modifier le lien html (<a href=""></a>)
$kanbanController = new KanbanController();
$kanban = null;
if (isset($slug)) {
	$kanban = $kanbanController->getKanbanBySlug($slug);
	if (is_null($kanban)) {
		header('Location: ' . $router->url('error404'));
		exit();
	}
	// Un utilisateur anonyme peut accèder au kanban si celui-ci est publique
	if ($kanban->getVisiblity() != Kanban::getVisibilityPublic()) {
		try {
			Session::checkSession();
		} catch (ForbiddenException $e) {
			Session::destroy();
			header('Location: ' . $router->url('error404'));
			exit();
		}
	}
} else {
	header('Location: ' . $router->url('error404'));
	exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="stylesheet" type="text/css" href="<?= join(DIRECTORY_SEPARATOR, array('..', ASSET_PATH, 'bootstrap-4.5.3-dist', 'css', 'bootstrap.min.css')) ?>">
		<link rel="stylesheet" type="text/css" href="<?= join(DIRECTORY_SEPARATOR, array('..', ASSET_PATH, 'css', 'dark-mode.css')) ?>">
		<?php $favicon_path = join(DIRECTORY_SEPARATOR, array('..', ASSET_PATH, 'favicon')); ?>
		<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'favicon.php')); ?>
		<title>Okanban | Mes kanbans</title>
	</head>
	<body class="bg-light">
		<header>	
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'navbar.php')); ?>
		</header>
		<div class="container-fluid">
			<div class="d-flex justify-content-sm-center justify-content-lg-between">
				<div class="overflow-auto">
					<h1 class="h3 m-3"><?= $kanban->getTitle() ?></h1>
				</div>
				<div>
					<div id="divInvitation" class="btn-toolbar m-3" role="toolbar" aria-label="Invitation">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text" id="InputInvitation">@</div>
							</div>
							<input id="iptInvitation" type="text" class="form-control" placeholder="Nom d'utilisateur" aria-label="Input invitation" aria-describedby="InputInvitation">
						</div>
						<button type="submit" class="btn btn-secondary ml-1" id="btnInvitation">Inviter</button>
					</div>
				</div>
			</div>
			<div id="basemodel" class="container-fluid"></div>
		</div>
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'footer.php')); ?>
		<noscript><p>Cette application nécessite l'activation de JavaScript.</p></noscript>
		<script type="text/javascript" src="<?= join(DIRECTORY_SEPARATOR, array('..', ASSET_PATH, 'bootstrap-4.5.3-dist', 'jquery-3.2.1.min.js')) ?>"></script>
		<script type="text/javascript" src="<?= join(DIRECTORY_SEPARATOR, array('..', ASSET_PATH, 'bootstrap-4.5.3-dist', 'js', 'bootstrap.bundle.min.js')) ?>"></script>
		<script type="text/javascript" src="<?= join(DIRECTORY_SEPARATOR, array('..', ASSET_PATH, 'js', 'dark-mode-switch.js')) ?>"></script>
		<script type="text/javascript" src="<?= join(DIRECTORY_SEPARATOR, array('..', ASSET_PATH, 'js', 'kanban.js')) ?>"></script>
	</body>
</html>
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'loadingtime.php')); ?>