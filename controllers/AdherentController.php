<?php
// controllers/AdherentController.php

require_once __DIR__ . '/../models/EmpruntModel.php';

class AdherentController {
    private $empruntModel;

    public function __construct() {
        $this->empruntModel = new EmpruntModel();
    }

    public function dashboard() {
        // Sécurité : seul un adhérent peut accéder
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'adherent') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Récupérer l'ID adhérent à partir de l'utilisateur connecté
        $adherentId = $this->getAdherentId($_SESSION['user_id']);

        if (!$adherentId) {
            echo "<div class='alert alert-danger'>Erreur : profil adhérent introuvable.</div>";
            return;
        }

        // Récupérer les données réelles depuis EmpruntModel
        $empruntsEnCours   = $this->empruntModel->getEmpruntsEnCours($adherentId);
        $empruntsRetardes  = $this->getEmpruntsRetardes($adherentId);
        $nbEmpruntsTotaux  = $this->empruntModel->getNbEmpruntsTotaux($adherentId);
        $nbEnCours        = $this->empruntModel->getNbEmpruntsTotaux($adherentId, 'en_cours');  
        // Charger la vue
        require_once ROOT . '/views/adherent/dashboard.php';
    }

    /**
     * Récupère l'ID adhérent depuis l'ID utilisateur
     */
    private function getAdherentId($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM adherents WHERE utilisateur_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchColumn() ?: null;
    }

    /**
     * Compte les emprunts en retard (date_retour_prevue passée + non rendu)
     */
    private function getEmpruntsRetardes($adherentId) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT * FROM emprunts 
            WHERE adherent_id = :adherent_id 
              AND statut = 'en_cours' 
              AND date_retour_prevue < CURDATE()
        ");
        $stmt->execute(['adherent_id' => $adherentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}