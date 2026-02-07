<?php
// DocumentController.php
// ────────────────────────────────────────────────
// PAS de session_start() ici → déjà appelé dans index.php

require_once __DIR__ . '/../models/DocumentModel.php';

class DocumentController {
    private $model;

    public function __construct() {
        $this->model = new DocumentModel();
    }

    /**
     * Affiche la liste des documents
     */
    public function index() {
        // Sécurité (optionnelle ici car déjà gérée par index.php, mais conservée)
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $documents = $this->model->getAll();

        // Chemin ABSOLU vers la vue → utilise ROOT
        require_once ROOT . '/views/documents/index2.php';  
        // ↑ Si ton fichier s'appelle index.php (et non index2.php), change en :
        // require_once ROOT . '/views/documents/index.php';
    }
      /**
 * Affiche le formulaire de modification d'un document
 */
/**
 * Affiche le formulaire de modification d'un document et traite la soumission
 */
public function modifier($id) {
    // Sécurité : admin ou bibliothécaire seulement
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'bibliothecaire'])) {
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }

    $document = $this->model->getById($id);
    if (!$document) {
        echo "<div class='alert alert-danger'>Document introuvable.</div>";
        return;
    }

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupère les données du formulaire (ou garde les anciennes si vide)
        $data = [
            'titre'       => trim($_POST['titre']       ?? $document['titre']),
            'auteur'      => trim($_POST['auteur']      ?? $document['auteur']),
            'type'        => trim($_POST['type']        ?? $document['type']),
            'description' => trim($_POST['description'] ?? $document['description']),
            'quantite'    => (int)($_POST['quantite']   ?? $document['quantite'])
        ];

        // Validation minimale
        if (empty($data['titre']) || empty($data['auteur'])) {
            $error = "Le titre et l'auteur sont obligatoires.";
        } else {
            // Enregistre les modifications
            if ($this->model->modifier($id, $data)) {
                // Succès → redirection vers la liste
                header('Location: ' . BASE_URL . 'documents');
                exit;
            } else {
                $error = "Erreur lors de la mise à jour du document.";
            }
        }
    }

    // Si pas de POST ou erreur → affiche le formulaire avec les valeurs actuelles
    require_once ROOT . '/views/documents/modifier.php';
}

/**
 * Supprime un document (seulement admin)
 * @param int $id ID du document à supprimer
 */
public function supprimer($id) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }

    $model = new DocumentModel();
    $document = $model->getById($id);

    if (!$document) {
        echo "<div class='alert alert-danger'>Document introuvable.</div>";
        return;
    }

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Confirmation POST reçue → on supprime
        if ($model->supprimer($id)) {
            $success = "Le document \"" . htmlspecialchars($document['titre']) . "\" a été supprimé avec succès.";
            header('Location: ' . BASE_URL . 'documents?success=' . urlencode($success));
            exit;
        } else {
            $error = "Erreur lors de la suppression du document.";
        }
    }

    // Affichage de la confirmation avant suppression
    require_once ROOT . '/views/documents/supprimer.php';
}
    /**
     * Ajoute un nouveau document
     */
    public function ajouter() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre       = trim($_POST['titre']       ?? '');
            $auteur      = trim($_POST['auteur']      ?? '');
            $type        = trim($_POST['type']        ?? '');
            $description = trim($_POST['description'] ?? '');
            $quantite    = (int)($_POST['quantite'] ?? 1);

            if (empty($titre) || empty($auteur)) {
                $error = "Le titre et l'auteur sont obligatoires.";
            } else {
                $data = [
                    'titre'       => $titre,
                    'auteur'      => $auteur,
                    'type'        => $type,
                    'description' => $description,
                    'quantite'    => $quantite
                ];

                if ($this->model->ajouter($data)) {
                    header('Location: ' . BASE_URL . 'documents');
                    exit;
                } else {
                    $error = "Erreur lors de l'ajout en base de données.";
                }
            }
            
        }

        require_once ROOT . '/views/documents/ajouter.php';
    }


 public function rechercher() {
    // Sécurité : obligatoire de se connecter pour rechercher/emprunter
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }

    $resultats = [];
    $query = trim($_GET['q'] ?? '');

    if ($query !== '') {
        $model = new DocumentModel();
        $resultats = $model->rechercher($query);  // Ta méthode rechercher déjà existante
    }

    require_once ROOT . '/views/documents/rechercher.php';
}

    // Ajoute ici les méthodes modifier() et supprimer() quand tu en auras besoin
} 