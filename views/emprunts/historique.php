<?php include ROOT . '/views/layout/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Mon historique d'emprunts</h2>

    <?php if (empty($emprunts)): ?>
        <div class="alert alert-info">
            Vous n'avez encore emprunté aucun document.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID Emprunt</th>
                    <th>Document</th> <!-- À adapter selon jointure -->
                    <th>Date emprunt</th>
                    <th>Date retour prévue</th>
                    <th>Date retour réelle</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emprunts as $emprunt): ?>
                    <tr>
                        <td><?= htmlspecialchars($emprunt['id'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($emprunt['document_titre'] ?? 'Document #' . $emprunt['document_id']) ?></td>
                        <td><?= htmlspecialchars($emprunt['date_emprunt'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($emprunt['date_retour_prevue'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($emprunt['date_retour_reelle'] ?? '—') ?></td>
                        <td>
                            <span class="badge <?= $emprunt['statut'] === 'retourne' ? 'bg-success' : 'bg-warning' ?>">
                                <?= htmlspecialchars($emprunt['statut'] ?? 'en cours') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include ROOT . '/views/layout/footer.php'; ?>