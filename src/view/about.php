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
		<title>Okanban | À propos de Okanban</title>
	</head>
	<body class="bg-light">
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'navbar.php')); ?>
		<div class="container-fluid p-5">
			<div class="p-2">
				<h1 class="h1 display-4 text-center">Présentation du projet</h1>
				<h2 class="h2">1 Le projet</h2>
				<h3 class="h3">1.1 Description</h3>
				<p class="text-justify">
					En gestion de projets, un Kanban est un tableau rendant compte de l’état d’avancement des tâches d’un
					projet. Les colonnes du tableau représentent chacune un état différent (ex : à faire, en cours, terminée, attribuée
					à X, etc.). Ce tableau est partagé et permet à chaque membre de l’équipe de prendre en charge une tâche et de
					la déplacer dans la colonne correspondant à son état actuel.
					Comme vous utilisez très certainement cette technique pour vos projets annuels, il n’est pas nécessaire
					d’entrer d’avantage dans les détails.
				</p>
				<h3 class="h3">1.2 Fonctionnalités attendues</h3>
				<p class="text-justify">
					La plateforme sera multi-utilisateurs et multi-projets (plusieurs kanbans par utilisateur). Une tâche sera
					décrite par une description (uniquement du texte, court et pouvant accepter uniquement les mises en formes
					gras, italique, souligné, barré) et une affectation (l’utilisateur en charge de la tâche ou « non affectée »), ainsi
					qu’une date limite de réalisation optionnelle.
					Tout utilisateur connecté (identifié) pourra :
				</p>
				<ul>
					<li>
						- créer un nouveau Kanban dont il sera le gestionnaire. Le nombre ainsi que l’intitulé des colonnes seront
						déterminés lors de la création du kanban avec deux colonnes obligatoires : Stories (à gauche) et terminées
						(à droite). Un kanban pourra être marqué « public » et sera ainsi visible de tous les utilisateurs, y compris
						les utilisateurs anonymes (non connectés) ;
					</li>
					<li>- inviter des utilisateurs de la plateforme à un kanban dont il est le gestionnaire ;</li>
					<li>- s’affecter une tâche dans tout kanban où il est invité ou affecter une tâche à un autre utilisateur invité d’un kanban s’il est gestionnaire de celui-ci ;</li>
					<li>- demander l’affichage de :
						<ul>
							<li>- la liste des kanbans qu’il gère,</li>
							<li>- la liste des kanbans auxquels ils participent,</li>
							<li>- la liste des tâches qui lui sont affectées pour un kanban donné,</li>
							<li>- la liste des tâches qui lui sont affectées globalement.</li>
							<li>Ces informations pourront être triées (ordre alphabétique, date limite de réalisation, etc.) ;</li>
						</ul>
					</li>
					<li>- déplacer une tâche vers une autre colonne si celle-ci lui est affectée ou s’il est le gestionnaire du kanban.</li>
				</ul>
				<p class="text-justify">Un utilisateur anonyme pourra seulement, lorsqu’il accède à la plateforme, voir la liste des kanbans publics et l’interface de connexion.</p>
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