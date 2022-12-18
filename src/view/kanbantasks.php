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
use src\controller\CardController;
use src\controller\TaskController;
use src\controller\form\KanbanTasksFormController as KTFC;
use src\model\Task;
use src\model\TaskDAO;

$key = [
	'searchbar' => 'search'
];
$data = [];
$data[$key['searchbar']] = '';
$kanban = null;
$tasks = array();
if (isset($_GET[$key['searchbar']])) {
	if (!empty($_GET[$key['searchbar']])) {
		$data[$key['searchbar']] = UF::test_input($_GET[$key['searchbar']]);
		$kanbanController = new KanbanController();
		$kanban = $kanbanController->getMyKanbanByTitle($data[$key['searchbar']]);
		if (!is_null($kanban)) {
			$cardController = new CardController();
			$cards = $cardController->getCardsByKanbanId($kanban->getId());
			$taskController = new TaskController();
			foreach ($cards as $card) {
				$tmpTasks = $taskController->getTasksByCardId($card->getId());
				foreach ($tmpTasks as $tmpTask) {
					array_push($tasks, $tmpTask);
				}
			}
			$keySort = KTFC::getKeySort();
			foreach ($keySort as $k => $v) {
				if (isset($_GET[$v]) && !empty($_GET[$v])) {
					$tmpKeySort = UF::test_input($k);
					$classTask = Task::CLASS;
					$columnName = '';
					$order = 'ASC';
					if (strcmp($tmpKeySort, KTFC::getKeySortByTitleAscendant()) == 0) {
						$columnName = TaskDAO::getColumnNameTitle($classTask);
					} else if (strcmp($tmpKeySort, KTFC::getKeySortByTitleDescendant()) == 0) {
						$columnName = TaskDAO::getColumnNameTitle($classTask);
						$order = 'DESC';
					} else if (strcmp($tmpKeySort, KTFC::getKeySortByCreatedAtAscendant()) == 0) {
						$columnName = TaskDAO::getColumnNameCreatedAt($classTask);
					} else if (strcmp($tmpKeySort, KTFC::getKeySortByCreatedAtDescendant()) == 0) {
						$columnName = TaskDAO::getColumnNameCreatedAt($classTask);
						$order = 'DESC';
					} else if (strcmp($tmpKeySort, KTFC::getKeySortByUpdatedAtAscendant()) == 0) {
						$columnName = TaskDAO::getColumnNameUpdatedAt($classTask);
					} else if (strcmp($tmpKeySort, KTFC::getKeySortByUpdatedAtDescendant()) == 0) {
						$columnName = TaskDAO::getColumnNameUpdatedAt($classTask);
						$order = 'DESC';
					} else if (strcmp($tmpKeySort, KTFC::getKeySortByDescriptionAscendant()) == 0) {
						$columnName = TaskDAO::getColumnNameDescription($classTask);
					} else if (strcmp($tmpKeySort, KTFC::getKeySortByDescriptionDescendant()) == 0) {
						$columnName = TaskDAO::getColumnNameDescription($classTask);
						$order = 'DESC';
					} else if (strcmp($tmpKeySort, KTFC::getKeySortByLimitDateAscendant()) == 0) {
						$columnName = TaskDAO::getColumnNameLimitDate($classTask);
					} else if (strcmp($tmpKeySort, KTFC::getKeySortByLimitDateDescendant()) == 0) {
						$columnName = TaskDAO::getColumnNameLimitDate($classTask);
						$order = 'DESC';
					} else {
						$columnName = TaskDAO::getColumnNameTitle($classTask);
					}
					$tasks = $taskController->getTasksByKanbanIdOrdered($kanban->getId(), $columnName, $order);
					break;
				}
			}
		}
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
		<title>Okanban | Mes tâches pour un kanban</title>
	</head>
	<body class="bg-light">
		<header>	
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'navbar.php')); ?>
		</header>
		<div class="container-fluid p-5">
			<div class="row justify-content-center">
				<div class="col-lg-8 col-md-12 col-sm-12 order-lg-first">
					<h1 class="h1 display-4 text-center">Mes tâches pour un kanban donnée</h1>
					<form action="" method="get" class="form-inline justify-content-center">
						<input type="search" id="searchbar" name="<?= $key['searchbar'] ?>" class="form-control mr-sm-2" value="<?= $data[$key['searchbar']] ?>" placeholder="Rechercher un kanban" required>
						<button class="btn btn-outline-primary my-2 my-sm-0" type="submit">Rechercher un kanban</button>
<?php if (isset($_GET[$key['searchbar']])) : ?>
	<?php if (!is_null($kanban)) : ?>
		<?php if (!empty($tasks)) : ?>
						<div class="table-responsive m-1">
							<table class="table table-hover text-nowrap">
								<thead>
									<tr>
										<th scope="col">
											Nom
											<input type="submit" id="<?= $keySort[KTFC::getKeySortByTitleAscendant()] ?>" name="<?= $keySort[KTFC::getKeySortByTitleAscendant()] ?>" value="&#8593;">
											<input type="submit" id="<?= $keySort[KTFC::getKeySortByTitleDescendant()] ?>" name="<?= $keySort[KTFC::getKeySortByTitleDescendant()] ?>" value="&#8595;">
										</th>
										<th scope="col">
											Crée le
											<input type="submit" id="<?= $keySort[KTFC::getKeySortByCreatedAtAscendant()] ?>" name="<?= $keySort[KTFC::getKeySortByCreatedAtAscendant()] ?>" value="&#8593;">
											<input type="submit" id="<?= $keySort[KTFC::getKeySortByCreatedAtDescendant()] ?>" name="<?= $keySort[KTFC::getKeySortByCreatedAtDescendant()] ?>" value="&#8595;">
										</th>
										<th scope="col">
											Dernière date de modification
											<input type="submit" id="<?= $keySort[KTFC::getKeySortByUpdatedAtAscendant()] ?>" name="<?= $keySort[KTFC::getKeySortByUpdatedAtAscendant()] ?>" value="&#8593;">
											<input type="submit" id="<?= $keySort[KTFC::getKeySortByUpdatedAtDescendant()] ?>" name="<?= $keySort[KTFC::getKeySortByUpdatedAtDescendant()] ?>" value="&#8595;">
										</th>
										<th scope="col">
											Description
											<input type="submit" id="<?= $keySort[KTFC::getKeySortByDescriptionAscendant()] ?>" name="<?= $keySort[KTFC::getKeySortByDescriptionAscendant()] ?>" value="&#8593;">
											<input type="submit" id="<?= $keySort[KTFC::getKeySortByDescriptionDescendant()] ?>" name="<?= $keySort[KTFC::getKeySortByDescriptionDescendant()] ?>" value="&#8595;">
										</th>
										<th scope="col">
											Date limite
											<input type="submit" id="<?= $keySort[KTFC::getKeySortByLimitDateAscendant()] ?>" name="<?= $keySort[KTFC::getKeySortByLimitDateAscendant()] ?>" value="&#8593;">
											<input type="submit" id="<?= $keySort[KTFC::getKeySortByLimitDateDescendant()] ?>" name="<?= $keySort[KTFC::getKeySortByLimitDateDescendant()] ?>" value="&#8595;">
										</th>
									</tr>
								</thead>
								<tbody>
			<?php foreach ($tasks as $task) : ?>
									<tr>
										<th scope="row"><?= $task->getTitle() ?></th>
										<td><?= date("d/m/Y \\à H:i ", strtotime($task->getCreatedAt())) ?></td>
										<td><?= date("d/m/Y \\à H:i ", strtotime($task->getUpdatedAt())) ?></td>
										<td class="text-truncate"><?= htmlspecialchars_decode($task->getDescription()) ?></td>
										<td><?= !is_null($task->getLimitDate()) ? date("d/m/Y \\à H:i ", strtotime($task->getLimitDate())) : ""?></td>
									</tr>
			<?php endforeach ?>
								</tbody>
							</table>
						</div>
		<?php endif ?>
	<?php else : ?>
						<h2 class="h2 text-center">Aucun résultat trouvé pour <?= $data[$key['searchbar']] ?> :(</h2>
	<?php endif ?>
<?php endif ?>
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