<?php
require_once '../Models/Database.php';
require_once '../Models/Enseignant.php';

class TeacherController {
    private $enseignant;

    public function __construct() {
        $this->enseignant = new Enseignant('Enseignant', 'teacher@teacher.com', 'password', new Role(2, 'Enseignant'), 'active');
    }

    public function listeCoursCrees() {
        return $this->enseignant->listeCoursCrees();
    }

    public function ajouterCours($title, $description, $content, $image, $type, $status, $categorie_id, $tags, $additionalData = []) {
        return $this->enseignant->ajouterCours($title, $description, $content, $image, $type, $status, $categorie_id, $tags, $additionalData);
    }

    public function afficheDetails($coursId) {
        return $this->enseignant->afficheDetails($coursId);
    }

    public function modifierCours($coursId, $data) {
        return $this->enseignant->modifierCours($coursId, $data);
    }

    public function supprimerCours($coursId) {
        return $this->enseignant->supprimerCours($coursId);
    }

    public function consulterStatistiques() {
        return $this->enseignant->consulterStatistiques();
    }

    public function getEtudiantsInscrits() {
        return $this->enseignant->getEtudiantsInscrits();
    }
}