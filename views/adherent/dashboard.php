<?php include ROOT . '/views/layout/header.php'; ?>

<div class="container mt-5">
    <h2>Bienvenue, <?= htmlspecialchars($_SESSION['user_nom']) ?> !</h2>
    <p class="lead">Votre espace adhérent</p>

    <div class="row mt-4">
        <!-- Carte 1 : Emprunts en cours -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Emprunts en cours</div>
                <div class="card-body">
                    <h5 class="card-title"><?= count($empruntsEnCours) ?></h5>
                    <p class="card-text">Documents actuellement empruntés</p>
                    <a href="<?= BASE_URL ?>emprunts/historique" class="btn btn-light">Voir la liste</a>
                </div>
            </div>
        </div>

        <!-- Carte 2 : Emprunts en retard -->
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Emprunts en retard</div>
                <div class="card-body">
                    <h5 class="card-title"><?= count($empruntsRetardes) ?></h5>
                    <p class="card-text">À rendre rapidement !</p>
                    <?php if (count($empruntsRetardes) > 0): ?>
                        <a href="<?= BASE_URL ?>emprunts/historique" class="btn btn-light">Voir</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Carte 3 : Total emprunts -->
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Historique total</div>
                <div class="card-body">
                    <h5 class="card-title"><?= $nbEmpruntsTotaux ?></h5>
                    <p class="card-text">Documents empruntés au total</p>
                    <a href="<?= BASE_URL ?>emprunts/historique" class="btn btn-light">Voir l'historique</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bouton principal : Chercher / emprunter -->
    <div class="text-center mt-5">
        <a href="<?= BASE_URL ?>documents/rechercher" class="btn btn-lg btn-primary">
            <i class="bi bi-search"></i> Rechercher et emprunter un document
        </a>
    </div>
</div>

<?php include ROOT . '/views/layout/footer.php'; ?>