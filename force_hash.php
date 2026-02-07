<?php
$mdp_clair = 'aderhent2026';   // ← mets ICI le mot de passe que TU VAS TAPER dans le formulaire
echo "Mot de passe que tu vas utiliser : " . $mdp_clair . "<br>";
echo "Hash à copier dans la base (colonne mot_de_passe) : ";
echo password_hash($mdp_clair, PASSWORD_DEFAULT);
?>