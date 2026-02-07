<?php include ROOT . '/views/layout/header.php'; ?>

<div class="container mt-5">
    <h2>Ajouter un document</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="titre" class="form-label">Titre *</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
        </div>

        <div class="mb-3">
            <label for="auteur" class="form-label">Auteur *</label>
            <input type="text" class="form-control" id="auteur" name="auteur" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select class="form-select" id="type" name="type">
                <option value="livre">Livre</option>
                <option value="cd">CD</option>
                <option value="dvd">DVD</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
        </div>

        <div class="mb-3">
         <label for="quantite" class="form-label">Quantité disponible</label>
         <input type="number" class="form-control" id="quantite" name="quantite" min="0" 
           value="<?= htmlspecialchars($document['quantite'] ?? 1) ?>">
         <div class="form-text">Nombre d'exemplaires disponibles. Mettez 0 si épuisé.</div>
     </div>

        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="<?= BASE_URL ?>documents" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include ROOT . '/views/layout/footer.php'; ?>