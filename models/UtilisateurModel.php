<?php
require_once dirname(__DIR__) . '/config/db.php';

class UtilisateurModel {
    public function authentifier($email, $mdp) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    error_log("Authentification - Email saisi : '$email'");
    if ($user) {
        error_log("Utilisateur trouvé - ID: {$user['id']}, Role: {$user['role']}");
        $hashEnBase = $user['mot_de_passe'];
        error_log("Hash en base commence par : " . substr($hashEnBase, 0, 7));
        if (password_verify($mdp, $hashEnBase)) {
            error_log("Mot de passe CORRECT");
            return $user;
        } else {
            error_log("Mot de passe INCORRECT");
        }
    } else {
        error_log("Aucun utilisateur pour cet email");
    }
    return false;
}

   public function ajouterUtilisateur($data) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (:nom, :prenom, :email, :mdp, :role)");
    $data['mdp'] = password_hash($data['mdp'], PASSWORD_DEFAULT);
    $success = $stmt->execute($data);

    if ($success && $data['role'] === 'adherent') {
        // Récupère l'ID nouvellement créé
        $userId = $pdo->lastInsertId();

        // Crée le profil adhérent (adresse et téléphone vides ou par défaut)
        $stmtAdh = $pdo->prepare("INSERT INTO adherents (utilisateur_id, adresse, telephone) VALUES (:user_id, '', '')");
        $stmtAdh->execute(['user_id' => $userId]);
    }

    return $success;
}

    // Méthodes supplémentaires pour modifier/supprimer un utilisateur (comme demandé dans le sujet pour gestion complète)
    public function modifierUtilisateur($id, $data) {
        global $pdo;
        $query = "UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email, role = :role";
        if (!empty($data['mdp'])) {
            $query .= ", mot_de_passe = :mdp";
            $data['mdp'] = password_hash($data['mdp'], PASSWORD_DEFAULT);
        }
        $query .= " WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function supprimerUtilisateur($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Méthode pour récupérer un utilisateur par ID (utile pour gestion)
    public function getById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getByEmail($email) {
     global $pdo;
     $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
     $stmt->execute(['email' => $email]);
     return $stmt->fetch(PDO::FETCH_ASSOC);
    }


  public function getAll() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, nom, prenom, email, role, date_inscription FROM utilisateurs ORDER BY date_inscription DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>