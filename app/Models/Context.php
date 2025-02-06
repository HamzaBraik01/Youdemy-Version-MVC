<?php
require_once 'Database.php';
require_once 'Cours.php';

class Context extends Course {
    private $objectif;

    public function __construct($id, $title, $description, $content, $image, $status, $objectif) {
        parent::__construct($id, $title, $description, $content, $image, 'CONTEXTE', $status);
        $this->objectif = $objectif;
    }

    public function getObjectif() {
        return $this->objectif;
    }

    public function afficheDetails() {
        echo "Titre : $this->title, Description : $this->description, Objectif : $this->objectif\n";
    }

    public function afficheCourse() {
        echo "Affichage du cours contextuel : $this->title\n";
    }

    public function ajouterCourse($categorie_id) {
        $db = Database::getInstance()->getConnection();
    
        // Commencer une transaction
        $db->beginTransaction();
    
        try {
            // Insérer le cours dans la table `cours`
            $query = "INSERT INTO cours (titre, description, contenu, type, image, status, categorie_id) 
                    VALUES (:titre, :description, :contenu, :type, :image, :status, :categorie_id)";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':titre' => $this->title,
                ':description' => $this->description,
                ':contenu' => $this->content,
                ':type' => $this->type,
                ':image' => $this->image,
                ':status' => $this->status,
                ':categorie_id' => $categorie_id
            ]);
    
            // Récupérer l'ID du cours inséré
            $cours_id = $db->lastInsertId();  
    
            // Insérer le contexte dans la table `contexte`
            $contexteQuery = "INSERT INTO contexte (objectif, cours_id) VALUES (:objectif, :cours_id)";
            $contexteStmt = $db->prepare($contexteQuery);
            $contexteStmt->execute([
                ':objectif' => $this->objectif,
                ':cours_id' => $cours_id
            ]);
    
            // Valider la transaction
            $db->commit();
    
            return $cours_id; // Retourner l'ID du cours
        } catch (Exception $e) {
            // En cas d'erreur, annuler la transaction
            $db->rollBack();
            error_log("Erreur lors de l'ajout du cours contextuel: " . $e->getMessage());
            throw new Exception("Erreur lors de l'ajout du cours contextuel.");
        }
    }
}
?>