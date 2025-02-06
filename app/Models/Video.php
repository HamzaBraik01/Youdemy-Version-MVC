<?php
require_once 'Database.php';
require_once 'Cours.php';

class Video extends Course {
    private $url;

    public function __construct($id, $title, $description, $content, $image, $status, $url) {
        parent::__construct($id, $title, $description, $content, $image, 'VIDEO', $status);
        $this->url = $url;
    }

    public function getUrl() {
        return $this->url;
    }

    public function afficheDetails() {
        echo "Titre : $this->title, Description : $this->description, URL : $this->url\n";
    }

    public function afficheCourse() {
        echo "Affichage du cours vidéo : $this->title\n";
    }

    public function ajouterCourse($categorie_id) {
        $db = Database::getInstance()->getConnection();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Commencer une transaction
        $db->beginTransaction();
    
        try {
            // Vérifiez les données avant l'insertion
            if (empty($this->title) || empty($this->type) || empty($categorie_id)) {
                throw new Exception("Les champs titre, type et categorie_id sont obligatoires.");
            }
    
            // Insérer le cours dans la table `cours` sans le champ `contenu`
            $query = "INSERT INTO cours (titre, description, type, image, status, categorie_id) 
                    VALUES (:titre, :description, :type, :image, :status, :categorie_id)";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':titre' => $this->title,
                ':description' => $this->description,
                ':type' => $this->type,
                ':image' => $this->image,
                ':status' => $this->status,
                ':categorie_id' => $categorie_id
            ]);
    
            // Récupérer l'ID du cours inséré
            $cours_id = $db->lastInsertId();
    
            // Insérer la vidéo dans la table `video`
            $videoQuery = "INSERT INTO video (url, cours_id) VALUES (:url, :cours_id)";
            $videoStmt = $db->prepare($videoQuery);
            $videoStmt->execute([
                ':url' => $this->url,
                ':cours_id' => $cours_id
            ]);
    
            // Valider la transaction
            $db->commit();
    
            return $cours_id; // Retourner l'ID du cours
        } catch (Exception $e) {
            // En cas d'erreur, annuler la transaction
            $db->rollBack();
            error_log("Erreur lors de l'ajout du cours vidéo: " . $e->getMessage());
            throw new Exception("Erreur lors de l'ajout du cours vidéo: " . $e->getMessage());
        }
    }
}
?>