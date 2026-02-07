<?php
// EmpruntController.php

require_once __DIR__ . '/../models/EmpruntModel.php';
require_once __DIR__ . '/../models/DocumentModel.php';

class EmpruntController {
    private $empruntModel;

    public function __construct() {
        $this->empruntModel = new EmpruntModel();
    }

    /**
     * Affiche l'historique des emprunts pour l'adhérent connecté
     */
    public function historique() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'adherent') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $adherentId = $this->getAdherentId($_SESSION['user_id']);
        if (!$adherentId) {
            echo "<div class='alert alert-danger'>Erreur : profil adhérent non trouvé.</div>";
            return;
        }

        $emprunts = $this->empruntModel->getHistoriqueParAdherent($adherentId);

        require_once ROOT . '/views/emprunts/historique.php';
    }

    /**
     * Permet à un adhérent d'emprunter un document
     * @param int $documentId ID du document à emprunter
     */
    public function emprunter($documentId) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'adherent') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $adherentId = $this->getAdherentId($_SESSION['user_id']);

        if (!$adherentId) {
            echo "<div class='alert alert-danger'>Profil adhérent introuvable.</div>";
            return;
        }

        $documentModel = new DocumentModel();

        // 1. Vérifier si le document existe et est disponible
        $document = $documentModel->getById($documentId);
        if (!$document) {
            echo "<div class='alert alert-danger'>Document introuvable.</div>";
            return;
        }
        if (!$document['disponible']) {
            echo "<div class='alert alert-warning'>Ce document n'est pas disponible pour le moment.</div>";
            return;
        }

        // 2. Vérifier si l'adhérent n'a pas déjà cet emprunt en cours
        if ($this->hasEmpruntEnCours($adherentId, $documentId)) {
            echo "<div class='alert alert-warning'>Vous avez déjà emprunté ce document.</div>";
            return;
        }

        // 3. Date de retour prévue : +21 jours (modifiable)
        $dateRetourPrevue = date('Y-m-d', strtotime('+21 days'));
        $dateRetourAffichee = date('d/m/Y', strtotime($dateRetourPrevue));

        // 4. Enregistrer l'emprunt
        $data = [
            'document_id'        => $documentId,
            'adherent_id'        => $adherentId,
            'date_retour_prevue' => $dateRetourPrevue
        ];

    if ($this->empruntModel->emprunter($data)) {
    $documentModel->setDisponible($documentId, 0);

    $nomAdherent = addslashes(htmlspecialchars($_SESSION['user_nom'] ?? 'Adhérent'));
    $dateRetourAffichee = date('d/m/Y', strtotime($dateRetourPrevue));

    // Modal + script qui s'exécute immédiatement
    echo <<<HTML
    <div class="modal fade" id="empruntSuccessModal" tabindex="-1" aria-labelledby="empruntSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="empruntSuccessModalLabel">Emprunt réussi !</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="fs-5 mb-3"><strong>Bonjour {$nomAdherent},</strong></p>
                    <p>Votre document a été emprunté avec succès !</p>
                    <p class="fw-bold fs-5">Date de retour prévue : {$dateRetourAffichee}</p>
                    <p class="text-muted mt-3">Merci de respecter la date pour éviter des pénalités.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <a href="' . BASE_URL . 'emprunts/historique" class="btn btn-primary">Voir mes emprunts</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const modalElement = document.getElementById('empruntSuccessModal');
            if (modalElement && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                console.error('Bootstrap Modal non disponible ou modal non trouvé');
                // Fallback alert si JS échoue
                alert('Emprunt réussi !\\nBonjour ' + '{$nomAdherent}' + ',\\nDate de retour prévue : ' + '{$dateRetourAffichee}');
            }
        })();
    </script>
HTML;
} else {
    echo "<div class='alert alert-danger mt-4'>Erreur lors de l'emprunt. Veuillez réessayer.</div>";
}
    }

    /**
     * Vérifie si l'adhérent a déjà cet emprunt en cours
     */
    private function hasEmpruntEnCours($adherentId, $documentId) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM emprunts 
            WHERE adherent_id = :adherent_id 
              AND document_id = :document_id 
              AND statut = 'en_cours'
        ");
        $stmt->execute([
            'adherent_id' => $adherentId,
            'document_id' => $documentId
        ]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Récupère l'ID adhérent depuis l'ID utilisateur
     */
    private function getAdherentId($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM adherents WHERE utilisateur_id = :uid");
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchColumn() ?: null;
    }
}