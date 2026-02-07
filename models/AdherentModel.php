<?php
require_once dirname(__DIR__) . '../config/db.php';

class AdherentModel {
    public function getAll() {
        global $pdo;
        $stmt = $pdo->query("SELECT a.*, u.nom, u.prenom FROM adherents a JOIN utilisateurs u ON a.utilisateur_id = u.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouter($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO adherents (utilisateur_id, adresse, telephone) VALUES (:utilisateur_id, :adresse, :telephone)");
        return $stmt->execute($data);
    }

    // Modifier/supprimer similaires à DocumentModel
}