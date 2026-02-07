<?php include ROOT . '/views/layout/header.php'; ?>

<div class="container mt-5">
    <h2>Gestion des utilisateurs</h2>

    <?php if (empty($utilisateurs)): ?>
        <div class="alert alert-info">Aucun utilisateur trouvé.</div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Inscrit le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['nom']) ?></td>
                        <td><?= htmlspecialchars($user['prenom']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : ($user['role'] === 'bibliothecaire' ? 'bg-warning' : 'bg-success') ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($user['date_inscription'])) ?></td>
                        <td>
                            <!-- À implémenter plus tard si besoin -->
                            <button class="btn btn-sm btn-info disabled">Modifier</button>
                            <button class="btn btn-sm btn-danger disabled">Supprimer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include ROOT . '/views/layout/footer.php'; ?>