<?php
session_start();

define('ROOT', __DIR__);
define('BASE_URL', '/Mediatheque/');

require_once ROOT . '/config/db.php';
require_once ROOT . '/models/UtilisateurModel.php';
require_once ROOT . '/controllers/AuthController.php';

$controller = new AuthController();
$controller->login(); // appelle directement login()
?>