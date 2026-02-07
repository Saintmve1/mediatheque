<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Médiathèque</title>

    <link href="<?= BASE_URL ?>public/css/bootstrap.min.css" rel="stylesheet">
    <script src="<?= BASE_URL ?>public/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= BASE_URL ?>">Médiathèque</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Utilisateur connecté : menu adapté au rôle -->
            <div class="navbar-nav ms-auto">
                <span class="nav-link">
                    Bonjour, <?= htmlspecialchars($_SESSION['user_nom'] ?? 'Utilisateur') ?>
                    <?php if (isset($_SESSION['user_role'])): ?>
                        <small class="text-muted">(<?= ucfirst($_SESSION['user_role']) ?>)</small>
                    <?php endif; ?>
                </span>

                <?php $role = $_SESSION['user_role'] ?? ''; ?>

                <?php if ($role === 'adherent'): ?>
                    <a class="nav-link" href="<?= BASE_URL ?>adherent/dashboard">Mon espace</a>
                    <a class="nav-link" href="<?= BASE_URL ?>documents/rechercher">Rechercher</a>
                    <a class="nav-link" href="<?= BASE_URL ?>emprunts/historique">Mes emprunts</a>
                <?php endif; ?>

                <?php if ($role === 'bibliothecaire' || $role === 'admin'): ?>
                    <a class="nav-link" href="<?= BASE_URL ?>documents">Documents</a>
                <?php endif; ?>

                <?php if ($role === 'admin'): ?>
                    <a class="nav-link" href="<?= BASE_URL ?>admin/utilisateurs">Utilisateurs</a>
                <?php endif; ?>

                <a class="nav-link" href="<?= BASE_URL ?>auth/logout">Déconnexion</a>
            </div>
        <?php else: ?>
            <!-- Non connecté : lien simple vers login -->
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= BASE_URL ?>auth/login">Se connecter</a>
                <a class="nav-link" href="<?= BASE_URL ?>auth/register">S'inscrire</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<div class="container mt-4">