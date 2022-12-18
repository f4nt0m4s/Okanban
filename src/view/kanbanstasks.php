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
use src\controller\TaskController;
use src\controller\form\KanbansTasksFormController as KSTFC;
use src\model\TaskDAO;
use src\model\Task;

$taskController = new TaskController();
$tasks = $taskController->getMyTasks();
$keySort = KSTFC::getKeySort();

foreach ($keySort as $k => $v) {
	if (isset($_GET[$v]) && !empty($_GET[$v])) {
		$tmpKeySort = UF::test_input($k);
		$classTask = Task::CLASS;
		$columnName = '';
		$order = 'ASC';
		if (strcmp($tmpKeySort, KSTFC::getKeySortByTitleAscendant()) == 0) {
			$columnName = TaskDAO::getColumnNameTitle($classTask);
		} else if (strcmp($tmpKeySort, KSTFC::getKeySortByTitleDescendant()) == 0) {
			$columnName = TaskDAO::getColumnNameTitle($classTask);
			$order = 'DESC';
		} else if (strcmp($tmpKeySort, KSTFC::getKeySortByCreatedAtAscendant()) == 0) {
			$columnName = TaskDAO::getColumnNameCreatedAt($classTask);
		} else if (strcmp($tmpKeySort, KSTFC::getKeySortByCreatedAtDescendant()) == 0) {
			$columnName = TaskDAO::getColumnNameCreatedAt($classTask);
			$order = 'DESC';
		} else if (strcmp($tmpKeySort, KSTFC::getKeySortByUpdatedAtAscendant()) == 0) {
			$columnName = TaskDAO::getColumnNameUpdatedAt($classTask);
		} else if (strcmp($tmpKeySort, KSTFC::getKeySortByUpdatedAtDescendant()) == 0) {
			$columnName = TaskDAO::getColumnNameUpdatedAt($classTask);
			$order = 'DESC';
		} else if (strcmp($tmpKeySort, KSTFC::getKeySortByDescriptionAscendant()) == 0) {
			$columnName = TaskDAO::getColumnNameDescription($classTask);
		} else if (strcmp($tmpKeySort, KSTFC::getKeySortByDescriptionDescendant()) == 0) {
			$columnName = TaskDAO::getColumnNameDescription($classTask);
			$order = 'DESC';
		} else if (strcmp($tmpKeySort, KSTFC::getKeySortByLimitDateAscendant()) == 0) {
			$columnName = TaskDAO::getColumnNameLimitDate($classTask);
		} else if (strcmp($tmpKeySort, KSTFC::getKeySortByLimitDateDescendant()) == 0) {
			$columnName = TaskDAO::getColumnNameLimitDate($classTask);
			$order = 'DESC';
		} else {
			$columnName = TaskDAO::getColumnNameTitle($classTask);
		}
		$tasks = $taskController->getMyTasksOrdered($columnName, $order);
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
		<title>Okanban | Toutes mes tâches</title>
	</head>
	<body class="bg-light">
		<header>	
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'navbar.php')); ?>
		</header>
		<div class="container-fluid p-5">
			<div class="row justify-content-center">
				<div class="col-lg-10 col-md-12 col-sm-12 order-lg-first">
					<h1 class="h1 display-4 text-center">Toutes mes tâches</h1>
<?php if (!empty($tasks)) : ?>
					<div class="table-responsive">
						<form action="#" method="get">
							<table class="table table-striped text-nowrap">
								<thead>
									<tr>
										<th scope="col">
											Nom
											<input type="submit" id="<?= $keySort[KSTFC::getKeySortByTitleAscendant()] ?>" name="<?= $keySort[KSTFC::getKeySortByTitleAscendant()] ?>" value="&#8593;">
											<input type="submit" id="<?= $keySort[KSTFC::getKeySortByTitleDescendant()] ?>" name="<?= $keySort[KSTFC::getKeySortByTitleDescendant()] ?>" value="&#8595;">
										</th>
										<th scope="col">
											Crée le
											<input type="submit" id="<?= $keySort[KSTFC::getKeySortByCreatedAtAscendant()] ?>" name="<?= $keySort[KSTFC::getKeySortByCreatedAtAscendant()] ?>" value="&#8593;">
											<input type="submit" id="<?= $keySort[KSTFC::getKeySortByCreatedAtDescendant()] ?>" name="<?= $keySort[KSTFC::getKeySortByCreatedAtDescendant()] ?>" value="&#8595;">
										</th>
										<th scope="col">
											Dernière date de modification
											<input type="submit" id="<?= $keySort[KSTFC::getKeySortByUpdatedAtAscendant()] ?>" name="<?= $keySort[KSTFC::getKeySortByUpdatedAtAscendant()] ?>" value="&#8593;">
											<input type="submit" id="<?= $keySort[KSTFC::getKeySortByUpdatedAtDescendant()] ?>" name="<?= $keySort[KSTFC::getKeySortByUpdatedAtDescendant()] ?>" value="&#8595;">
										</th>
										<th scope="col">
											Description
											<input type="submit" id="<?= $keySort[KSTFC::getKeySortByDescriptionAscendant()] ?>" name="<?= $keySort[KSTFC::getKeySortByDescriptionAscendant()] ?>" value="&#8593;">
											<input type="submit" id="<?= $keySort[KSTFC::getKeySortByDescriptionDescendant()] ?>" name="<?= $keySort[KSTFC::getKeySortByDescriptionDescendant()] ?>" value="&#8595;">
										</th>
										<th scope="col">
											Date limite
											<input type="submit" id="<?= $keySort[KSTFC::getKeySortByLimitDateAscendant()] ?>" name="<?= $keySort[KSTFC::getKeySortByLimitDateAscendant()] ?>" value="&#8593;">
											<input type="submit" id="<?= $keySort[KSTFC::getKeySortByLimitDateDescendant()] ?>" name="<?= $keySort[KSTFC::getKeySortByLimitDateDescendant()] ?>" value="&#8595;">
										</th>
									</tr>
								</thead>
								<tbody>
	<?php foreach ($tasks as $task) : ?>
									<tr>
										<td><?= $task->getTitle() ?></td>
										<td><?= date("d/m/Y \\à H:i ", strtotime($task->getCreatedAt())) ?></td>
										<td><?= date("d/m/Y \\à H:i ", strtotime($task->getUpdatedAt())) ?></td>
										<td class="text-truncate"><?= $task->getDescription() ?></td>
										<td><?= !is_null($task->getLimitDate()) ? date("d/m/Y \\à H:i ", strtotime($task->getLimitDate())) : ""?></td>
									</tr>
	<?php endforeach ?>
								</tbody>
							</table>
						</form>
					</div>
<?php endif ?>
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