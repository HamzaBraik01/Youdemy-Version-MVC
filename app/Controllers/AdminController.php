<?php
require_once '../Models/Database.php';
require_once '../Models/Administrateur.php';

class AdminController {
    private $admin;

    public function __construct() {
        $this->admin = new Administrateur('Admin', 'admin@admin.com', 'password', new Role(1, 'Administrateur'), 'active');
    }

    public function validerCompteEnseignant($id, $action) {
        return $this->admin->validerCompteEnseignant($id, $action);
    }

    public function gererUtilisateurs() {
        return $this->admin->gererUtilisateurs();
    }

    public function insererTagsEnMasse($tags) {
        return $this->admin->insererTagsEnMasse($tags);
    }

    public function consulterStatistiquesGlobales() {
        return $this->admin->consulterStatistiquesGlobales();
    }

    public function gererContenu() {
        return $this->admin->gererContenu();
    }

    public function gererCategories() {
        return $this->admin->gererCategories();
    }
}