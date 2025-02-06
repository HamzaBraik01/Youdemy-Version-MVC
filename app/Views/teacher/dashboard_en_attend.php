<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Role.php';
require_once '../../classes/Enseignant.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Enseignant') {
    header('Location: ../../public/login.php');
    exit();
}
$_SESSION['user']['role_id']=2;


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Enseignant - En Attente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.1/feather.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-indigo-700 text-white w-64 flex flex-col">
            <div class="p-5">
                <h2 class="text-2xl font-bold">Espace Enseignant</h2>
                <p class="text-indigo-200 text-sm mt-1"><?php echo htmlspecialchars($_SESSION['user']['nom']); ?></p>
            </div>
            <nav class="flex-1">
                <a href="#" class="flex items-center px-6 py-3 text-indigo-100 hover:bg-indigo-800">
                    <i data-feather="info" class="mr-3"></i>
                    En Attente
                </a>
            </nav>
            <!-- Déconnexion -->
            <div class="p-5 border-t border-indigo-600">
                <a href="../../assets/php/logout.php" class="flex items-center px-6 py-3 text-indigo-100 hover:bg-indigo-800">
                    <i data-feather="log-out" class="mr-3"></i>
                    Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-800">Tableau de Bord</h1>
                </div>
            </header>

            <main class="p-6">
                <!-- Message d'attente -->
                <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8 text-center">
                    <i data-feather="clock" class="mx-auto h-16 w-16 text-indigo-600"></i>
                    <h2 class="mt-4 text-2xl font-bold text-gray-800">En Attente d'Approval</h2>
                    <p class="mt-2 text-gray-600">
                        Votre compte est en cours de vérification par l'administrateur. Vous serez notifié dès que votre compte sera approuvé.
                    </p>
                    <p class="mt-4 text-gray-500 text-sm">
                        Si vous avez des questions, veuillez contacter l'administrateur à <a href="mailto:admin@admin.com" class="text-indigo-600 hover:underline">admin@admin.com</a>.
                    </p>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Initialize Feather Icons
        feather.replace();
    </script>
</body>
</html>