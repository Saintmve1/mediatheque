<?php
require_once dirname(__DIR__) . '../config/db.php';
class EmpruntModel {
    public function emprunter($data) {
        global $pdo;
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO emprunts (document_id, adherent_id, date_retour_prevue) VALUES (:document_id, :adherent_id, :date_retour_prevue)");
            $stmt->execute($data);
           $updateDoc = $pdo->prepare("UPDATE documents SET quantite = quantite + 1 WHERE id = :document_id");
           $updateDoc->execute(['document_id' => $docId]);
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollback();
            return false;
        }
    }

    public function retourner($id) {
        global $pdo;
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE emprunts SET date_retour_reelle = NOW(), statut = 'retourne' WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $docId = $this->getEmpruntById($id)['document_id'];
            $updateDoc = $pdo->prepare("UPDATE documents SET disponible = 1 WHERE id = :document_id");
            $updateDoc->execute(['document_id' => $docId]);
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollback();
            return false;
        }
    }

    public function getEmpruntsEnCours($adherentId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT e.*, d.titre AS document_titre 
        FROM emprunts e 
        JOIN documents d ON e.document_id = d.id 
        WHERE e.adherent_id = :adherent_id 
          AND e.statut = 'en_cours'
        ORDER BY e.date_emprunt DESC
    ");
    $stmt->execute(['adherent_id' => $adherentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

   public function getHistoriqueParAdherent($adherent_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT e.*, d.titre AS document_titre 
        FROM emprunts e 
        JOIN documents d ON e.document_id = d.id 
        WHERE e.adherent_id = :adherent_id 
        ORDER BY e.date_emprunt DESC
    ");
    $stmt->execute(['adherent_id' => $adherent_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    private function getEmpruntById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM emprunts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getNbEmpruntsTotaux($adherentId, $statut = null) {
    global $pdo;
    $sql = "SELECT COUNT(*) FROM emprunts WHERE adherent_id = :adherent_id";
    $params = ['adherent_id' => $adherentId];

    if ($statut !== null) {
        $sql .= " AND statut = :statut";
        $params['statut'] = $statut;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}
}
