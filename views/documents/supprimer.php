<?php include ROOT . '/views/layout/header.php'; ?>

<div class="container mt-5">
    <h2>Confirmer la suppression</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Voulez-vous vraiment supprimer ce document ?</h5>
            <p class="card-text">
                <strong>Titre :</strong> <?= htmlspecialchars($document['titre']) ?><br>
                <strong>Auteur :</strong> <?= htmlspecialchars($document['auteur']) ?><br>
                <strong>Type :</strong> <?= htmlspecialchars($document['type']) ?><br>
                <strong>Quantité :</strong> <?= htmlspecialchars($document['quantite'] ?? 0) ?>
            </p>

            <p class="text-danger">Cette action est irréversible !</p>

            <form method="POST">
                <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                <a href="<?= BASE_URL ?>documents" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>
</div>

<?php include ROOT . '/views/layout/footer.php'; ?>