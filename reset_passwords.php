<?php
require_once 'config/db.php';

$users = [
    'admin@mediatheque.fr'      => 'admin123',
    'biblio@mediatheque.fr'     => 'biblio123',
    'adherent1@mediatheque.fr'  => 'adh111',
    'adherent2@mediatheque.fr'  => 'adh222',
    'jean.dupont@test.com'      => 'jean123',
    'testadh@gmail.com'         => 'test123'   // change si tu veux
];

foreach ($users as $email => $plainPassword) {
    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = :hash WHERE email = :email");
    $stmt->execute(['hash' => $hash, 'email' => $email]);
    echo "Mot de passe mis à jour pour $email → $plainPassword<br>";
}

echo "<h2>Terminé !</h2>";
?>