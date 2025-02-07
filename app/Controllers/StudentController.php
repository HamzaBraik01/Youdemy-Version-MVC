<?php
require_once '../Models/Database.php';
require_once '../Models/Etudiant.php';

class StudentController {
    private $etudiant;

    public function __construct() {
        $this->etudiant = new Etudiant('Etudiant', 'student@student.com', 'password', new Role(3, 'Etudiant'), 'active');
    }

    public function listeCours($limit = 6, $page = 1, $category_id = null) {
        return $this->etudiant->listeCours($limit, $page, $category_id);
    }

    public function listeCoursInscrits() {
        return $this->etudiant->listeCoursInscrits();
    }

    public function sInscrireAuCours($etudiant_id, $cours_id) {
        return $this->etudiant->sInscrireAuCours($etudiant_id, $cours_id);
    }

    public function consulterMesCours() {
        return $this->etudiant->consulterMesCours();
    }

    public function showCours($course_id) {
        return $this->etudiant->showCours($course_id);
    }
}