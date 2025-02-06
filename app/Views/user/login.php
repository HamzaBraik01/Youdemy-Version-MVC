<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/Utilisateur.php';
require_once '../classes/Administrateur.php';
require_once '../classes/Enseignant.php';
require_once '../classes/Etudiant.php';
require_once '../classes/Role.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Récupérer l'utilisateur depuis la base de données
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
            // Afficher un message d'erreur avec SweetAlert pour un compte suspendu
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
                $user->seConnecter(); // Démarre la session et redirige l'utilisateur
            }
        }
    } else {
        // Afficher un message d'erreur avec SweetAlert pour un email ou mot de passe incorrect
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
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
        font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-gray-50 to-white min-h-screen flex flex-col">
    <nav class="bg-gray-900 border-b border-gray-800">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center h-20">
                    <a href="index.php" class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                        <span class="text-2xl font-bold text-white">Youdemy</span>
                    </a>
                </div>
            </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto">
            <div class="bg-white p-8 rounded-2xl shadow-lg">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Welcome back</h2>
                    <p class="text-gray-600 mt-2">Please enter your details to sign in</p>
                </div>
                <form class="space-y-6" action="" method="POST">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address
                        </label>
                        <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors"
                        placeholder="Enter your email"
                        />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                        </label>
                        <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors"
                        placeholder="Enter your password"
                        />
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="remember-me"
                            name="remember-me"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        />
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                        </div>

                        <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Forgot password?
                        </a>
                    </div>

                    <button
                        type="submit"
                        class="w-full flex justify-center py-3 px-4 rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors font-medium"
                    >
                        Sign in
                    </button>

                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl hover:border-gray-400 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        </button>
                        <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl hover:border-gray-400 transition-colors">
                        <svg class="h-5 w-5 text-black" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.6.11.819-.26.819-.578 0-.284-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61-.546-1.386-1.332-1.755-1.332-1.755-1.087-.744.083-.729.083-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 21.795 24 17.298 24 12c0-6.627-5.373-12-12-12"/>
                        </svg>
                        </button>
                    </div>
                </form>

                <p class="mt-8 text-center text-sm text-gray-600">
                Don't have an account?
                <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Create one now
                </a>
                </p>
            </div>
        </div>
    </main>

    <footer class="bg-gray-900 text-gray-300 py-12 mt-24">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-white font-semibold text-lg mb-4">About</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Press</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold text-lg mb-4">Solutions</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition-colors">For Students</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">For Teachers</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">For Business</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">For Schools</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold text-lg mb-4">Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Cookie Settings</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold text-lg mb-4">Contact</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition-colors">Contact Us</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Support</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Partners</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Feedback</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                    <span class="text-xl font-bold text-white">Youdemy</span>
                </div>
                <p class="text-gray-500 text-sm">© 2024 Youdemy. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>