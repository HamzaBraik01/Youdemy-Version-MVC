<?php
require_once __DIR__ . '/../Models/Database.php';
require_once __DIR__ .'/../Models/Utilisateur.php';
require_once __DIR__ .'/../Models/Administrateur.php';
require_once __DIR__ .'/../Models/Enseignant.php';
require_once __DIR__ .'/../Models/Etudiant.php';
require_once __DIR__ .'/../Models/Role.php';

class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = trim($_POST['password']);

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                SELECT u.id, u.nom, u.email, u.motDePasse, u.status, r.id AS role_id, r.role 
                FROM Utilisateur u 
                INNER JOIN Role r ON u.role_id = r.id 
                WHERE u.email = ?
            ");
            $stmt->execute([$email]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData && password_verify($password, $userData['motDePasse'])) {
                if ($userData['status'] === 'suspendu') {
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Compte suspendu',
                                    text: 'Votre compte est banni. Se réinscrire ou contacter admin@admin.com',
                                });
                            });
                        </script>";
                } else {
                    $role = new Role($userData['role_id'], $userData['role']);
                    $user = null;

                    switch ($userData['role']) {
                        case 'Administrateur':
                            $user = new Administrateur($userData['nom'], $userData['email'], $password, $role, $userData['status']);
                            break;
                        case 'Enseignant':
                            $user = new Enseignant($userData['nom'], $userData['email'], $password, $role, $userData['status']);
                            break;
                        case 'Etudiant':
                            $user = new Etudiant($userData['nom'], $userData['email'], $password, $role, $userData['status']);
                            break;
                    }

                    if ($user) {
                        $user->setId($userData['id']);
                        $user->seConnecter();
                    }
                }
            } else {
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Email ou mot de passe incorrect.',
                            });
                        });
                    </script>";
            }
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: ../views/user/login.php');
        exit();
    }
}