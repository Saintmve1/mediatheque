<?php
require_once dirname(__DIR__) . '/config/db.php';

class DocumentModel {
    public function getAll() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM documents");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouter($data) {
     global $pdo;
     $stmt = $pdo->prepare("
        INSERT INTO documents 
        (titre, auteur, type, description, quantite) 
        VALUES (:titre, :auteur, :type, :description, :quantite)
      ");
      return $stmt->execute($data);
    }

    public function modifier($id, $data) {
        global $pdo;
        $stmt = $pdo->prepare("
            UPDATE documents 
            SET titre = :titre, 
                auteur = :auteur, 
                type = :type, 
                description = :description, 
                quantite = :quantite 
            WHERE id = :id
        ");
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function supprimer($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM documents WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function rechercher($critere) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT * FROM documents 
            WHERE titre LIKE :critere 
               OR auteur LIKE :critere 
               OR type LIKE :critere
        ");
        $stmt->execute(['critere' => "%$critere%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    public function setDisponible($id, $disponible) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE documents SET disponible = :disponible WHERE id = :id");
        return $stmt->execute([
            'disponible' => (int)$disponible,
            'id' => $id
        ]);
    }

    public function decrementQuantite($id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE documents SET quantite = quantite - 1 WHERE id = :id AND quantite > 0");
        return $stmt->execute(['id' => $id]);
    }

    public function incrementQuantite($id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE documents SET quantite = quantite + 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function updateQuantite($id, $quantite) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE documents SET quantite = :quantite WHERE id = :id");
        return $stmt->execute(['quantite' => (int)$quantite, 'id' => $id]);
    }
}