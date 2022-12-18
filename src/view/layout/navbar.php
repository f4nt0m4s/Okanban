<?php 
use src\Session;
use src\exception\ForbiddenException;
$isLogged = false;
$user = null;
try {
	Session::checkSession();
	$isLogged = true;
	$user = unserialize($_SESSION[Session::getUserIndex()]);
} catch (ForbiddenException $e) {
	$isLogged = false;
}
?>
			<!--Navbar-->
			<nav class="navbar navbar-expand-lg navbar-light border-bottom box-shadow text-break shadow-sm">
				<div class="container-fluid">
					<a class="navbar-brand" href="<?= $router->url('home') ?>">Okanban</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="navbar-collapse">
						<ul class="navbar-nav mr-auto">
							<li class="nav-item">
								<a class="nav-link text-secondary" href="<?= $router->url('home') ?>">Accueil</a>
							</li>
<?php if ($isLogged) : ?>
							<li class="nav-item dropdown">
								<a id="dropdownMenuLinkKanban" class="nav-link dropdown-toggle" href="#" role="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Kanban</a>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuLinkKanban">
									<a class="dropdown-item" href="<?= $router->url('kanbans'); ?>">Mes kanbans</a>
									<a class="dropdown-item" href="<?= $router->url('kanbansparticipations'); ?>">Mes participations</a>
									<a class="dropdown-item" href="<?= $router->url('kanbantasks'); ?>">Mes tâches pour un kanban</a>
									<a class="dropdown-item" href="<?= $router->url('kanbanstasks'); ?>">Toutes mes tâches</a>
									<a class="dropdown-item" href="<?= $router->url('newkanban'); ?>">Nouveau kanban</a>
								</div>
							</li>
<?php endif ?>
							<li class="nav-item">
								<a class="nav-link text-secondary" href="<?= $router->url('about') ?>">À propos</a>
							</li>
						</ul>
<?php if ($isLogged) : ?>
						<div class="dropdown show">
							<a id="dropdownMenuLinkAccount" class="btn btn-secondary dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Mon compte</a>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLinkAccount">
								<small class="dropdown-item text-muted"><?= $user->getUsername() ?></small>
								<small class="dropdown-item text-muted"><?= $user->getEmail() ?></small>
								<hr class="m-1">
								<a class="dropdown-item" href="<?= $router->url('logout'); ?>">Se déconnecter</a>
							</div>
						</div>
<?php else : ?>
							<ul class="navbar-nav">
							<li class="nav-item me-2">
								<a class="nav-link text-secondary" href="<?= $router->url('login'); ?>">Connexion</a>
							</li>
							<li class="nav-item me-2">
								<a class="nav-link text-secondary" href="<?= $router->url('register'); ?>">Inscription</a>
							</li>
						</ul>
<?php endif ?>
					</div>
				</div>
			</nav>
