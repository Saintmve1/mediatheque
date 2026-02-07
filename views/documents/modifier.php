<?php include ROOT . '/views/layout/header.php'; ?>

<div class="container mt-5">
    <h2>Modifier le document</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>documents/modifier/<?= $document['id'] ?>">
        <div class="mb-3">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" 
                   value="<?= htmlspecialchars($document['titre'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="auteur" class="form-label">Auteur</label>
            <input type="text" class="form-control" id="auteur" name="auteur" 
                   value="<?= htmlspecialchars($document['auteur'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select class="form-select" id="type" name="type" required>
                <option value="livre" <?= ($document['type'] ?? '') === 'livre' ? 'selected' : '' ?>>Livre</option>
                <option value="cd" <?= ($document['type'] ?? '') === 'cd' ? 'selected' : '' ?>>CD</option>
                <option value="dvd" <?= ($document['type'] ?? '') === 'dvd' ? 'selected' : '' ?>>DVD</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($document['description'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
          <label for="quantite" class="form-label">Quantité initiale *</label>
          <input type="number" class="form-control" id="quantite" name="quantite" min="1" value="1" required>
          <div class="form-text">Nombre d'exemplaires disponibles au départ.</div>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="<?= BASE_URL ?>documents" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include ROOT . '/views/layout/footer.php'; ?>