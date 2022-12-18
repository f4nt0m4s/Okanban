<?php
use src\controller\LoginController;
LoginController::logout();
header('Location: ' . $router->url('home'));
exit();
?>