<?php
require_once __DIR__ . '/../models/UtilisateurModel.php';

class AdminController {
    private $model;

    public function __construct() {
        $this->model = new UtilisateurModel();
    }

    /**
     * Liste tous les utilisateurs (pour admin)
     */
    public function listeUtilisateurs() {
        $utilisateurs = $this->model->getAll();  // Méthode à ajouter dans UtilisateurModel

        require_once ROOT . '/views/admin/utilisateurs.php';
    }
}