<?php

use src\Session;
use src\exception\ForbiddenException;
try {
	// Si l'utilisateur est connecté, redirige vers la page accueil
	Session::checkSession();
	header('Location: ' . $router->url('home'));
	exit();
} catch (ForbiddenException $e) {}

use src\utility\UtilityFunction as UF;
use src\controller\RegisterController;
use src\form\Form;

$validatorError = [];
$error = [];
$data = [];
$key = [
	'username' => 'username',
	'email' => 'email',
	'password' => 'password',
	'confirmpassword' => 'confirmpassword'
];

foreach ($key as $k => $value) {
	$data[$k] = '';
}

if (isset($_POST)) {
	if (!empty($_POST)) {
		foreach ($key as $k => $value) {
			$data[$k] = isset($_POST[$k]) ? UF::test_input($_POST[$k]) : '';
		}
		$registerController = new RegisterController($data);
		$validatorError = $registerController->getValidatorError();
		if ($registerController->isValid()) {
			if ($registerController->register()) {
				header('Location: ' . $router->url('login'));
				exit();
			} else {
				$validatorError = $registerController->getValidatorError();
			}
		}
		foreach($validatorError as $k => $validationError) {
			$error[$k] = $validationError->__toString();
		}
	}
}
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
		<title>Okanban | Inscription</title>
	</head>
	<body class="bg-light">
		<header>
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'navbar.php')); ?>
		</header>
		<div class="container-fluid p-5">
			<form action="#" method="post" class="p-2">
				<h1 class="h1 display-4 text-center text-wrap">Inscription</h1>
				<hr class="w-25 mr-auto ml-auto">

				<!-- A remplir plustard pour la base de données -->
				<?php if (isset($error['insertion']) && !empty($error['insertion'])) : ?>
					<div class="form-row justify-content-center">
						<div class="form-group col-lg-4 col-md-8">	
							<div class="alert alert-danger mr-auto ml-auto" role="alert">
								<p class="p-0 m-0 text-center"><?= $error['insertion'] ?></p>
							</div>
						</div>
					</div>
				<?php endif ?>

				
				<div class="form-row justify-content-center">
					<div class="form-group col-lg-4 col-md-8">
						<?= $form->input('email', $key['email'], 'Adresse-email', 'Entrer une adresse email', $data[$key['email']]); ?>
					</div>
				</div>
				<div class="form-row justify-content-center">
					<div class="form-group col-lg-4 col-md-8">
						<?= $form->input('text', $key['username'], 'Nom d’utilisateur', 'Entrer un nom d\'utilisateur', $data[$key['username']]); ?>
					</div>
				</div>
				<div class="form-row justify-content-center">
					<div class="form-group col-lg-4 col-md-8">
						<?= $form->input('password', $key['password'], 'Mot de passe', 'Entrer votre mot de passe'); ?>
					</div>
				</div>
				<div class="form-row justify-content-center">
					<div class="form-group col-lg-4 col-md-8">
						<?= $form->input('password', $key['confirmpassword'], 'Confirmer votre mot de passe', 'Entrer à nouveau votre mot de passe'); ?>
					</div>
				</div>
				<div class="form-row justify-content-center">
					<div class="form-group col-lg-4 col-md-8 text-center">
						<input type="submit" id="submit" class="btn btn-primary" name="submit" value="S'inscrire">
					</div>
				</div>
				<div class="form-row justify-content-center">
					<div class="form-group col-lg-4 col-md-8 text-center">
						<span class="text-right">
							<a href="<?= $router->url('login'); ?>" class="text-decoration-none">Déja de compte ? Connectez-vous</a>
						</span>
					</div>
				</div>
			</form>
		</div>
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'footer.php')); ?>
		<noscript><p>Cette application nécessite l'activation de JavaScript.</p></noscript>
		<script type="text/javascript" src="<?= join(DIRECTORY_SEPARATOR, array(ASSET_PATH, 'bootstrap-4.5.3-dist', 'jquery-3.2.1.min.js')) ?>"></script>
		<script type="text/javascript" src="<?= join(DIRECTORY_SEPARATOR, array(ASSET_PATH, 'bootstrap-4.5.3-dist', 'js', 'bootstrap.bundle.min.js')) ?>"></script>
		<script type="text/javascript" src="<?= join(DIRECTORY_SEPARATOR, array(ASSET_PATH, 'js', 'dark-mode-switch.js')) ?>"></script>
	</body>
</html>
<?php include join(DIRECTORY_SEPARATOR, array(VIEW_PATH, 'layout', 'loadingtime.php')); ?>