<?php
require_once '../Models/Database.php';
require_once '../Models/Utilisateur.php';
require_once '../Models/Administrateur.php';
require_once '../Models/Enseignant.php';
require_once '../Models/Etudiant.php';
require_once '../Models/Role.php';

class RegisterController {
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = htmlspecialchars(trim($_POST['name']));
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = trim($_POST['password']);
            $roleStr = $_POST['role'];

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT COUNT(*) FROM Utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Cet email est déjà utilisé. Veuillez utiliser un autre email.',
                            });
                        });
                    </script>";
            } else {
                $roleId = ($roleStr === 'Enseignant') ? 2 : 3;
                $role = new Role($roleId, $roleStr);

                if ($roleStr === 'Enseignant') {
                    $user = new Enseignant($nom, $email, $password, $role, 'en attente');
                } else {
                    $user = new Etudiant($nom, $email, $password, $role, 'active');
                }

                $user->register();

                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: 'Votre compte a été créé avec succès !',
                            }).then(() => {
                                window.location.href = 'login.php';
                            });
                        });
                    </script>";
            }
        }
    }
}