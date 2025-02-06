<?php
require_once 'Role.php';
require_once 'Database.php';
abstract class Utilisateur {
    protected int $id;
    protected string $nom;
    protected string $email;
    protected string $motDePasse;
    protected Role $role;
    protected string $status;

    // Constructeur
    public function __construct(string $nom, string $email, string $motDePasse, Role $role, string $status = 'active') {
        $this->nom = $nom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->role = $role;
        $this->status = $status;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getMotDePasse(): string {
        return $this->motDePasse;
    }

    public function getRole(): Role {
        return $this->role;
    }

    public function getStatus(): string {
        return $this->status;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setMotDePasse(string $motDePasse): void {
        $this->motDePasse = $motDePasse;
    }

    public function setRole(Role $role): void {
        $this->role = $role;
    }

    public function setStatus(string $status): void {
        $this->status = $status;
    }

    // Méthode pour se connecter
    public function seConnecter(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user'] = [
            'id' => $this->id,
            'nom' => $this->nom,
            'email' => $this->email,
            'role' => $this->role->getRole(), 
            'status' => $this->status
        ];

        $this->redirigerUtilisateur();
    }

    public function seDeconnecter(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['user']);

        session_destroy();


    }

    abstract public function register(): void;

    protected function save(): void {
        $db = Database::getInstance()->getConnection();
    
        // Hachage sécurisé du mot de passe
        $motDePasseHash = password_hash($this->motDePasse, PASSWORD_BCRYPT);
        $roleId = $this->role->getId();
    
        $stmt = $db->prepare("
            INSERT INTO Utilisateur (nom, email, motDePasse, role_id, status) 
            VALUES (:nom, :email, :motDePasse, :role_id, :status)
        ");
    
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':motDePasse', $motDePasseHash);
        $stmt->bindParam(':role_id', $roleId);
        $stmt->bindParam(':status', $this->status);
    
        $stmt->execute();
    
        $this->id = $db->lastInsertId();
    }
    

    protected function redirigerUtilisateur(): void {
        // Vérifier si l'utilisateur est un enseignant et que son statut est "en attente"
        if ($this->role->getRole() === 'Enseignant' && $this->status === 'en attente') {
            header('Location: ../views/teacher/dashboard_en_attend.php');
            exit();
        }

        // Redirection en fonction du rôle
        switch ($this->role->getRole()) {
            case 'Administrateur':
                header('Location: ../views/admin/dashboard.php');
                break;
            case 'Enseignant':
                if ($this->status === 'active') {
                    header('Location: ../views/teacher/dashboard.php');
                } else {
                    header('Location: ../public/login.php');
                }
                break;
            case 'Etudiant':
                header('Location: ../views/student/dashboard.php');
                break;
            default:
                header('Location: ../public/login.php');
                break;
        }
        exit();
    }
}