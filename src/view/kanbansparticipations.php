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
use src\controller\KanbanController;
use src\controller\form\KanbanFormController as KFC;
use src\model\KanbanDAO;
use src\model\Kanban;


$kanbanController = new KanbanController();
$kanbans = $kanbanController->getMyKanbansParticipations();
$keySort = KFC::getKeySort();

foreach ($keySort as $k => $v) {
	if (isset($_GET[$v]) && !empty($_GET[$v])) {
		$tmpKeySort = UF::test_input($k);
		$class = Kanban::CLASS;
		$columnName = '';
		$order = 'ASC';
		if (strcmp($tmpKeySort, KFC::getKeySortByTitleAscendant()) == 0) {
			$columnName = KanbanDAO::getColumnNameTitle($class);
		} else if (strcmp($tmpKeySort, KFC::getKeySortByTitleDescendant()) == 0) {
			$columnName = KanbanDAO::getColumnNameTitle($class);
			$order = 'DESC';
		} else if (strcmp($tmpKeySort, KFC::getKeySortByDateAscendant()) == 0) {
			$columnName = KanbanDAO::getColumnNameCreatedAt($class);
		} else if (strcmp($tmpKeySort, KFC::getKeySortByDateDescendant()) == 0) {
			$columnName = KanbanDAO::getColumnNameCreatedAt($class);
			$order = 'DESC';
		}
		$kanbans = $kanbanController->getMyKanbansParticipationsOrdered($columnName, $order);
		break;
	}
}

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
		<title>Okanban | Mes kanbans</title>
	</head>
	<body class="bg-light">
		<header>	
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'navbar.php')); ?>
		</header>
		<div class="container-fluid p-5">
			<div class="row justify-content-center">
				<div class="col-lg-8 col-md-12 col-sm-12 order-lg-first">
					<h2 class="h1 display-4 text-center">Mes participations aux kanbans</h2>
					<form action="#" method="get">
						<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
							<div class="btn-group" role="group">
								<button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Trié par</button>
								<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
									<input type="submit" id="<?= $keySort[KFC::getKeySortByTitleAscendant()] ?>" name="<?= $keySort[KFC::getKeySortByTitleAscendant()] ?>" class="dropdown-item" value="Nom du kanban croissant">
									<input type="submit" id="<?= $keySort[KFC::getKeySortByTitleDescendant()] ?>" name="<?= $keySort[KFC::getKeySortByTitleDescendant()] ?>" class="dropdown-item" value="Nom du kanban décroissant">
									<input type="submit" id="<?= $keySort[KFC::getKeySortByDateAscendant()] ?>" name="<?= $keySort[KFC::getKeySortByDateAscendant()] ?>" class="dropdown-item" value="Date de création croissant">
									<input type="submit" id="<?= $keySort[KFC::getKeySortByDateDescendant()] ?>" name="<?= $keySort[KFC::getKeySortByDateDescendant()] ?>" class="dropdown-item" value="Date de création décroissante">
								</div>
							</div>
						</div>
					</form>
<?php
$nbElements = count($kanbans);
$k = 0;
$maxColumns = 2;
$nbRows = intval(ceil($nbElements)) / $maxColumns;
for ($i = 0; $i < $nbRows; $i++) {
?>
					<div class="row">
<?php for ($j = 0; $j < $maxColumns && $k < $nbElements; $j++) {?>
						<div class="col-lg-6 col-md-6 col-sm-12 p-2">
							<div class="card bg-light">
								<div class="card-header">
									<p class="text-capitalize"><?= $kanbans[$k]->getTitle() ?></p>
								</div>
								<div class="card-body">
									<p class="card-text"><?= $kanbans[$k]->getTitle() ?></p>
									<a href="<?= $router->url('kanban', array('slug' => $kanbans[$k]->getSlug())) ?>" class="btn btn-primary">Ouvrir ce kanban</a>
								</div>
								<div class="card-footer">
									<small class="text-muted font-weight-bold"><?= date("\\C\\r\\é\\e \\l\\e d/m/Y \\à H:i ", strtotime($kanbans[$k]->getCreatedAt())) ?></small>
								</div>
							</div>
						</div>
<?php $k++; ?>
<?php } ?>
					</div>
<?php } ?>
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