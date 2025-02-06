<?php
require_once 'Database.php';
require_once 'Utilisateur.php';
require_once 'Video.php';
require_once 'Context.php';

class Enseignant extends Utilisateur {
    // Constructeur
    public function __construct(string $nom, string $email, string $motDePasse, Role $role, string $status = 'active') {
        parent::__construct($nom, $email, $motDePasse, $role, $status);  
    }

    // Enregistrer un enseignant
    public function register(): void {
        $this->save();
    }

    // Lister les cours créés par l'enseignant
    public function listeCoursCrees(): array {
        $db = Database::getInstance()->getConnection();
        $enseignantId = $_SESSION['user']['id'];
    
        $query = "
            SELECT c.*, cat.name AS categorie_nom, COUNT(sc.id_etudiant) AS nb_etudiants 
            FROM cours c
            JOIN categorie cat ON c.categorie_id = cat.id
            LEFT JOIN student_courses sc ON c.id = sc.id_cours
            JOIN enseignant_cours ec ON c.id = ec.id_cours
            WHERE ec.id_enseignant = :id_enseignant
            GROUP BY c.id
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([':id_enseignant' => $enseignantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter un cours
    public function ajouterCours($title, $description, $content, $image, $type, $status, $categorie_id, $tags, $additionalData = []) {
        // Vérifier le type de contenu
        if ($type === 'VIDEO') {
            // Créer une instance de Video
            $video = new Video(null, $title, $description, null, $image, $status, $additionalData['url']);
            $cours_id = $video->ajouterCourse($categorie_id); // Passer la catégorie sélectionnée
        } elseif ($type === 'CONTEXTE') {
            // Créer une instance de Context
            $context = new Context(null, $title, $description, $content, $image, $status, $additionalData['objectif']);
            $cours_id = $context->ajouterCourse($categorie_id); // Passer la catégorie sélectionnée
        } else {
            throw new Exception("Type de contenu non supporté.");
        }

        // Ajouter l'enseignant et le cours dans la table Enseignant_Cours
        $this->ajouterEnseignantCours($cours_id);

        // Ajouter les tags dans la table Course_Tag
        $this->ajouterTagsAuCours($cours_id, $tags);
    }

    // Ajouter un enseignant à un cours
    private function ajouterEnseignantCours($cours_id) {
        $db = Database::getInstance()->getConnection();

        // Récupérer l'ID de l'enseignant depuis la session
        $enseignant_id = $_SESSION['user']['id'];

        // Insérer dans la table Enseignant_Cours
        $query = "INSERT INTO Enseignant_Cours (id_enseignant, id_cours) VALUES (:id_enseignant, :id_cours)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':id_enseignant' => $enseignant_id,
            ':id_cours' => $cours_id
        ]);
    }

    // Ajouter des tags à un cours
    private function ajouterTagsAuCours($cours_id, $tags) {
        $db = Database::getInstance()->getConnection();

        // Insérer chaque tag sélectionné dans la table Course_Tag
        foreach ($tags as $tag_id) {
            $query = "INSERT INTO Course_Tag (id_tag, id_cours) VALUES (:id_tag, :id_cours)";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':id_tag' => $tag_id,
                ':id_cours' => $cours_id
            ]);
        }
    }

    // Afficher les détails d'un cours
    public function afficheDetails(int $coursId): array {
        $db = Database::getInstance()->getConnection();

        // Récupérer les informations de base du cours
        $query = "SELECT * FROM cours WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $coursId]);
        $cours = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cours) {
            throw new Exception("Cours non trouvé.");
        }

        // Récupérer les informations supplémentaires selon le type de cours
        if ($cours['type'] === 'VIDEO') {
            $query = "SELECT * FROM Video WHERE cours_id = :cours_id";
            $stmt = $db->prepare($query);
            $stmt->execute([':cours_id' => $coursId]);
            $details = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($cours['type'] === 'CONTEXTE') {
            $query = "SELECT * FROM Contexte WHERE cours_id = :cours_id";
            $stmt = $db->prepare($query);
            $stmt->execute([':cours_id' => $coursId]);
            $details = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            throw new Exception("Type de cours non supporté.");
        }

        // Fusionner les informations de base et les détails spécifiques
        return array_merge($cours, $details);
    }

    // Modifier un cours
    public function modifierCours(int $coursId, array $data): void {
        $db = Database::getInstance()->getConnection();

        // Vérifier si le cours existe
        $query = "SELECT * FROM cours WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $coursId]);
        $cours = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cours) {
            throw new Exception("Cours non trouvé.");
        }

        // Mettre à jour les informations de base du cours
        $query = "
            UPDATE cours 
            SET titre = :titre, description = :description, contenu = :contenu, 
                image = :image, status = :status, categorie_id = :categorie_id 
            WHERE id = :id
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':titre' => $data['titre'],
            ':description' => $data['description'],
            ':contenu' => $data['contenu'],
            ':image' => $data['image'],
            ':status' => $data['status'],
            ':categorie_id' => $data['categorie_id'],
            ':id' => $coursId
        ]);

        // Mettre à jour les informations supplémentaires selon le type de cours
        if ($cours['type'] === 'VIDEO') {
            $query = "UPDATE Video SET url = :url WHERE cours_id = :cours_id";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':url' => $data['url'],
                ':cours_id' => $coursId
            ]);
        } elseif ($cours['type'] === 'CONTEXTE') {
            $query = "UPDATE Contexte SET objectif = :objectif WHERE cours_id = :cours_id";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':objectif' => $data['objectif'],
                ':cours_id' => $coursId
            ]);
        } else {
            throw new Exception("Type de cours non supporté.");
        }
    }

    // Supprimer un cours
    public function supprimerCours(int $coursId): void {
        $db = Database::getInstance()->getConnection();
    
        // Vérifier si le cours existe
        $query = "SELECT * FROM cours WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $coursId]);
        $cours = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$cours) {
            throw new Exception("Cours non trouvé.");
        }
    
        // Supprimer les informations supplémentaires selon le type de cours
        if ($cours['type'] === 'VIDEO') {
            $query = "DELETE FROM Video WHERE cours_id = :cours_id";
            $stmt = $db->prepare($query);
            $stmt->execute([':cours_id' => $coursId]);
        } elseif ($cours['type'] === 'CONTEXTE') {
            $query = "DELETE FROM Contexte WHERE cours_id = :cours_id";
            $stmt = $db->prepare($query);
            $stmt->execute([':cours_id' => $coursId]);
        } else {
            throw new Exception("Type de cours non supporté.");
        }
    
        // Supprimer le cours de la table principale
        $query = "DELETE FROM cours WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $coursId]);
    }

    // Consulter les statistiques
    public function consulterStatistiques(): array {
        $enseignantId = $_SESSION['user']['id'];
        $db = Database::getInstance()->getConnection();

        // Requête pour récupérer le nombre total de cours créés par l'enseignant
        $queryCours = "SELECT COUNT(*) as total_cours FROM enseignant_cours WHERE id_enseignant = :id_enseignant";
        $stmtCours = $db->prepare($queryCours);
        $stmtCours->execute(['id_enseignant' => $enseignantId]);
        $totalCours = $stmtCours->fetch(PDO::FETCH_ASSOC)['total_cours'];

        // Requête pour récupérer le nombre total d'étudiants inscrits aux cours de l'enseignant
        $queryEtudiants = "
            SELECT COUNT(DISTINCT sc.id_etudiant) as total_etudiants 
            FROM student_courses sc
            JOIN enseignant_cours ec ON sc.id_cours = ec.id_cours
            WHERE ec.id_enseignant = :id_enseignant
        ";
        $stmtEtudiants = $db->prepare($queryEtudiants);
        $stmtEtudiants->execute(['id_enseignant' => $enseignantId]);
        $totalEtudiants = $stmtEtudiants->fetch(PDO::FETCH_ASSOC)['total_etudiants'];

        // Requête pour récupérer le nombre total de cours auxquels les étudiants sont inscrits
        $queryCoursInscrits = "
            SELECT COUNT(*) as total_cours_inscrits
            FROM student_courses sc
            JOIN enseignant_cours ec ON sc.id_cours = ec.id_cours
            WHERE ec.id_enseignant = :id_enseignant
        ";
        $stmtCoursInscrits = $db->prepare($queryCoursInscrits);
        $stmtCoursInscrits->execute(['id_enseignant' => $enseignantId]);
        $totalCoursInscrits = $stmtCoursInscrits->fetch(PDO::FETCH_ASSOC)['total_cours_inscrits'];

        // Requête pour récupérer le top 3 des cours les plus inscrits avec les détails
        $queryTopCours = "
            SELECT c.titre, c.image, cat.name as categorie, COUNT(sc.id_etudiant) as nb_inscriptions 
            FROM student_courses sc
            JOIN cours c ON sc.id_cours = c.id
            JOIN categorie cat ON c.categorie_id = cat.id
            JOIN enseignant_cours ec ON c.id = ec.id_cours
            WHERE ec.id_enseignant = :id_enseignant
            GROUP BY c.id
            ORDER BY nb_inscriptions DESC
            LIMIT 3
        ";
        $stmtTopCours = $db->prepare($queryTopCours);
        $stmtTopCours->execute(['id_enseignant' => $enseignantId]);
        $topCours = $stmtTopCours->fetchAll(PDO::FETCH_ASSOC);

        // Retourner les statistiques
        return [
            'total_cours' => $totalCours,
            'total_etudiants' => $totalEtudiants,
            'total_cours_inscrits' => $totalCoursInscrits, 
            'top_cours' => $topCours
        ];
    }
    public function getEtudiantsInscrits(): array {
        $db = Database::getInstance()->getConnection();
        $enseignantId = $_SESSION['user']['id'];
    
        $query = "
            SELECT u.nom, u.email, c.titre AS cours_titre 
            FROM utilisateur u
            JOIN student_courses sc ON u.id = sc.id_etudiant
            JOIN cours c ON sc.id_cours = c.id
            JOIN enseignant_cours ec ON c.id = ec.id_cours
            WHERE ec.id_enseignant = :id_enseignant
            GROUP BY u.id, c.titre
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([':id_enseignant' => $enseignantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}