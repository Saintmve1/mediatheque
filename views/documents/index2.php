<?php
// PAS de session_start() ici → déjà fait dans index.php

// Sécurité minimale si besoin (optionnel)
// if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . 'auth/login'); exit; }

// Protection contre variable non définie
if (!isset($documents) || !is_array($documents)) {
    $documents = [];
}
?>

<?php include ROOT . '/views/layout/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Liste des Documents</h2>

    <?php if (empty($documents)): ?>
        <div class="alert alert-info">
            Aucun document trouvé dans la base de données.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Type</th>
                    <th>Quantité restante</th>  <!-- ← NOUVELLE COLONNE -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td><?= htmlspecialchars($doc['id'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($doc['titre'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($doc['auteur'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($doc['type'] ?? '—') ?></td>
                        <td>
                            <?php
                            $q = $doc['quantite'] ?? 0;
                            $badge = ($q > 0) ? 'bg-success' : 'bg-danger';
                            ?>
                            <span class="badge <?= $badge ?>"><?= $q ?></span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>documents/modifier/<?= $doc['id'] ?>" 
                               class="btn btn-sm btn-warning">Modifier</a>
                            
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <a href="<?= BASE_URL ?>documents/supprimer/<?= $doc['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Vraiment supprimer ?');">Supprimer</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>documents/ajouter" class="btn btn-primary mt-3">
        <i class="bi bi-plus-lg"></i> Ajouter un document
    </a>
</div>

<?php include ROOT . '/views/layout/footer.php'; ?>