<?php
// index.php – Version corrigée pour stopper la boucle de redirection

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chemins (uniformisation casse → majuscules)
define('ROOT', __DIR__);
define('BASE_URL', '/MEDIATHEQUE/');   // ← Change ici et partout dans les redirections

// Chargement des dépendances de base
require_once ROOT . '/config/db.php';
require_once ROOT . '/models/UtilisateurModel.php';
require_once ROOT . '/controllers/AuthController.php';
// Ajoute ici les autres controllers quand tu les implémentes
// require_once ROOT . '/controllers/DocumentController.php';
// etc.

// ────────────────────────────────────────────────
// Nettoyage et normalisation de l'URL
// ────────────────────────────────────────────────
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = trim($requestUri, '/');

// Enlève le préfixe /MEDIATHEQUE/ (ou /Mediatheque/) si présent
$prefix = 'MEDIATHEQUE';   // ← Mets ici la casse exacte de ton dossier
if (strtoupper(substr($requestUri, 0, strlen($prefix))) === strtoupper($prefix)) {
    $requestUri = trim(substr($requestUri, strlen($prefix)), '/');
}

// Maintenant $requestUri est propre : '' ou 'auth/login' ou 'documents/index' etc.
$segments = explode('/', $requestUri ?: 'home/index'); // fallback si vide
$controllerName = $segments[0] ?? 'home';
$action       = $segments[1] ?? 'index';

// Debug temporaire (à commenter après test)
// error_log("URL demandée : {$_SERVER['REQUEST_URI']} → controller: $controllerName / action: $action");

// ────────────────────────────────────────────────
// Pages publiques (pas besoin de connexion)
$publicRoutes = ['auth', 'home'];  // ajoute 'home' si tu veux une page d'accueil publique

$isPublic = in_array($controllerName, $publicRoutes);

// Protection : redirige vers login si NON connecté ET page privée
if (!$isPublic && !isset($_SESSION['user_id'])) {
    // error_log("Redirection forcée vers login depuis $controllerName/$action");
    header('Location: ' . BASE_URL . 'auth/login');
    exit;
}

// ────────────────────────────────────────────────
// Gestion racine vide ou juste /MEDIATHEQUE/
if ($requestUri === '' || $requestUri === $prefix) {
    if (isset($_SESSION['user_id'])) {
        if (in_array($_SESSION['user_role'], ['admin', 'bibliothecaire'])) {
            header('Location: ' . BASE_URL . 'documents/index');
        } elseif ($_SESSION['user_role'] === 'adherent') {
            header('Location: ' . BASE_URL . 'emprunts/historique');
        } else {
            header('Location: ' . BASE_URL . 'auth/login');
        }
    } else {
        header('Location: ' . BASE_URL . 'auth/login');
    }
    exit;
}

// ────────────────────────────────────────────────
// Routage
// ────────────────────────────────────────────────
try {
    switch ($controllerName) {
        case 'auth':
            require_once ROOT . '/controllers/AuthController.php';
            $ctrl = new AuthController();

            if ($action === 'login' || $action === '') {
                $ctrl->login();
            } elseif ($action === 'logout') {
                $ctrl->logout();
            } elseif ($action === 'register') {
                $ctrl->register();
            } else {
                http_response_code(404);
                echo "<h1>404 - Action auth inconnue</h1>";
            }
            break;

        case 'documents':
    require_once ROOT . '/controllers/DocumentController.php';
    $ctrl = new DocumentController();

    if ($action === 'index' || $action === '') {
        $ctrl->index();
    } elseif ($action === 'ajouter') {
        $ctrl->ajouter();
    } elseif ($action === 'modifier' && isset($segments[2])) {
        $id = (int)$segments[2];
        $ctrl->modifier($id);
    } elseif ($action === 'supprimer' && isset($segments[2])) {
        $id = (int)$segments[2];
        $ctrl->supprimer($id);
    } elseif ($action === 'rechercher') {
        $ctrl->rechercher();
    } else {
        http_response_code(404);
        echo "<h1>404 - Action documents inconnue</h1>";
    }
    break;

        case 'emprunts':
            require_once ROOT . '/controllers/EmpruntController.php';
            $ctrl = new EmpruntController();

            if ($action === 'historique' || $action === '') {
                $ctrl->historique();
                } elseif ($action === 'emprunter' && isset($segments[2])) {
               $documentId = (int)$segments[2];
               $ctrl->emprunter($documentId);   // ← NOUVEAU : appel à la méthode emprunter
               } else {
                http_response_code(404);
                echo "<h1>404 - Action emprunts inconnue</h1>";
            }
            break;

        case 'adherent':
            require_once ROOT . '/controllers/AdherentController.php';
            $ctrl = new AdherentController();

            if ($action === 'dashboard' || $action === '') {
                $ctrl->dashboard();
            } else {
                http_response_code(404);
                echo "<h1>404 - Page adhérent inconnue</h1>";
            }
            break;

        default:
            if ($isPublic) {
                echo "<h1>Bienvenue sur la Médiathèque</h1>";
                echo "<p><a href='" . BASE_URL . "auth/login'>Se connecter</a></p>";
            } else {
                header('Location: ' . BASE_URL . 'auth/login');
                exit;
            }
            break;
        case 'admin':
    // Sécurité : seul admin peut accéder
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }

    require_once ROOT . '/controllers/AdminController.php';
    $ctrl = new AdminController();

    if ($action === 'utilisateurs' || $action === '') {
        $ctrl->listeUtilisateurs();
    } else {
        http_response_code(404);
        echo "<h1>404 - Page admin inconnue</h1>";
    }
    break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "<h1>Erreur serveur</h1><pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}