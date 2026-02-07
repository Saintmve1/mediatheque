<?php
// ────────────────────────────────────────────────
// views/auth/login.php – Version autonome pour test
// Active erreurs + session + chargement contrôleur
// ────────────────────────────────────────────────

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

define('ROOT', dirname(__DIR__, 2));  // remonte à la racine du projet – adapte si besoin
define('BASE_URL', '/MEDIATHEQUE/');  // Changé en majuscules

// Chargement des fichiers nécessaires
require_once ROOT . '/config/db.php';
require_once ROOT . '/models/UtilisateurModel.php';
require_once ROOT . '/controllers/AuthController.php';

// Appel du contrôleur
$controller = new AuthController();
$controller->login();

// PAS DE exit; ICI – le contrôleur gère l'inclusion de la vue
?>