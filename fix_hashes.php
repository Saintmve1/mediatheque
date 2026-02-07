<?php
require_once 'config/db.php';  // Vérifie que ce fichier existe et connecte bien à la BDD

echo "<h2>Régénération des hashes</h2><pre>";

$users = [
    15 => 'admin123',       // admin@mediatheque.fr
    16 => 'biblio456',      // biblio@mediatheque.fr
    17 => 'adh111',         // adherent1@mediatheque.fr
    18 => 'adh222',         // adherent2@mediatheque.fr
    19 => 'jean1234',       // jean.dupont@test.com
];

foreach ($users as $id => $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = :hash WHERE id = :id");
    $success = $stmt->execute([':hash' => $hash, ':id' => $id]);
    
    if ($success) {
        echo "ID $id mis à jour → Mot de passe : <strong>$password</strong>\n";
        echo "Nouveau hash : $hash\n\n";
    } else {
        echo "ÉCHEC ID $id : ";
        print_r($pdo->errorInfo());
        echo "\n";
    }
}

echo "</pre>";
echo "<p style='color:green'>Terminé ! Utilise les mots de passe ci-dessus pour te connecter.</p>";
echo "<p><strong>Supprime ce fichier après usage !</strong></p>";
?>