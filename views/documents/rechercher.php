<?php include ROOT . '/views/layout/header.php'; ?>

<div class="container mt-5">
    <h2>Rechercher un document</h2>

    <form method="GET" action="<?= BASE_URL ?>documents/rechercher" class="mb-4">
        <div class="input-group mb-3">
            <input type="text" name="q" class="form-control" placeholder="Titre, auteur ou type..." 
                   value="<?= htmlspecialchars($query ?? '') ?>" required>
            <button class="btn btn-primary" type="submit">Rechercher</button>
        </div>
    </form>

    <?php if (!empty($query)): ?>
        <h4>Résultats pour "<?= htmlspecialchars($query) ?>"</h4>

        <?php if (empty($resultats)): ?>
            <div class="alert alert-info">
                Aucun document trouvé. Essayez d'autres mots-clés.
            </div>
        <?php else: ?>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Type</th>
                        <th>Disponible</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultats as $doc): ?>
                        <tr>
                            <td><?= htmlspecialchars($doc['titre']) ?></td>
                            <td><?= htmlspecialchars($doc['auteur']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($doc['type'])) ?></td>
                            <td>
                                <?php if ($doc['disponible']): ?>
                                    <span class="badge bg-success">Disponible</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Indisponible</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($doc['disponible']): ?>
                                    <a href="<?= BASE_URL ?>emprunts/emprunter/<?= $doc['id'] ?>" 
                                       class="btn btn-sm btn-success">Emprunter</a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>Indisponible</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include ROOT . '/views/layout/footer.php'; ?>