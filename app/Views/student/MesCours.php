<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Role.php';
require_once '../../classes/Etudiant.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Etudiant') {
    header('Location: ../../public/login.php');
    exit();
}

$_SESSION['user']['role_id'] = 3;
$Etudiant = new Etudiant(
    $_SESSION['user']['nom'],
    $_SESSION['user']['email'],
    '', 
    new Role(3, $_SESSION['user']['role']),
    $_SESSION['user']['status']
);

// Récupérer les cours auxquels l'étudiant est inscrit
$coursInscrits = $Etudiant->listeCoursInscrits();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Étudiant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.1/feather.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-blue-600 text-white w-64 flex flex-col">
            <div class="p-5">
                <h2 class="text-2xl font-bold">Youdemy</h2>
                <p class="text-blue-200 text-sm mt-1"><?php echo $_SESSION['user']['nom']; ?></p>
            </div>
            <nav class="flex-1">
                <a href="dashboard.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-700">
                    <i data-feather="home" class="mr-3"></i>
                    Accueil
                </a>
                <a href="MesCours.php" class="flex items-center px-6 py-3 text-blue-100 bg-blue-700">
                    <i data-feather="book" class="mr-3"></i>
                    Mes Cours
                </a>
            </nav>
            <!-- Déconnexion -->
            <div class="p-5 border-t border-blue-500">
                <a href="../../assets/php/logout.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-700">
                    <i data-feather="log-out" class="mr-3"></i>
                    Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-800">Mes Cours Inscrits</h1>
                        <div class="relative">
                            <input type="text" placeholder="Rechercher un cours..." 
                                class="w-96 px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <i data-feather="search" class="absolute right-3 top-2.5 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </header>

            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- My Courses Section -->
                <section class="mb-12">
                    <h2 class="text-xl font-semibold mb-6">Mes Cours</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($coursInscrits as $cours): ?>
                            <!-- Course Card -->
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                <div class="relative pb-60">
                                    <img src="../../assets/uploads/<?php echo $cours['cours_image']; ?>" alt="Course thumbnail" class="absolute h-full w-full object-cover">
                                    <div class="absolute top-4 right-4">
                                        <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full">En cours</span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center mb-2">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full"><?php echo $cours['cours_type']; ?></span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($cours['cours_titre']); ?></h3>
                                    <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($cours['cours_description']); ?></p>
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <i data-feather="user" class="w-4 h-4 text-gray-600"></i>
                                            <span class="ml-2 text-sm text-gray-600">Dr.<?php echo htmlspecialchars($cours['enseignant_nom']); ?></span>
                                        </div>
                                        <div class="flex items-center text-yellow-400">
                                            <i data-feather="star" class="w-4 h-4 fill-current"></i>
                                            <span class="ml-1 text-sm">4.8</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t">
                                        <div class="flex justify-center"> 
                                            <a href="Course_Details.php?id=<?php echo $cours['cours_id']; ?>" class="inline-block">
                                                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                    Continuer
                                                </button>
                                            </a>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script>
        // Initialize Feather Icons
        feather.replace();
    </script>
</body>
</html>