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
$Enseignant = new Enseignant(
    $_SESSION['user']['nom'],
    $_SESSION['user']['email'],
    '', 
    new Role(2, $_SESSION['user']['role']),
    $_SESSION['user']['status']
);
$statistiques = $Enseignant->consulterStatistiques();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Enseignant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.1/feather.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-indigo-700 text-white w-64 flex flex-col">
            <div class="p-5">
                <h2 class="text-2xl font-bold">Espace Enseignant</h2>
                <p class="text-indigo-200 text-sm mt-1">Dr. <?php echo htmlspecialchars($_SESSION['user']['nom']); ?></p>
            </div>
            <nav class="flex-1">
                <a href="dashboard.php" class="flex items-center px-6 py-3 text-indigo-100 bg-indigo-800">
                    <i data-feather="bar-chart-2" class="mr-3"></i>
                    Statistiques
                </a>
                <a href="manage-courses.php" class="flex items-center px-6 py-3 text-indigo-100 hover:bg-indigo-800">
                    <i data-feather="plus-circle" class="mr-3"></i>
                    Nouveau Cours
                </a>
                <a href="my-courses.php" class="flex items-center px-6 py-3 text-indigo-100 hover:bg-indigo-800">
                    <i data-feather="book-open" class="mr-3"></i>
                    Mes Cours
                </a>
                <a href="Inscriptions-in-courses.php" class="flex items-center px-6 py-3 text-indigo-100 hover:bg-indigo-800">
                    <i data-feather="users" class="mr-3"></i>
                    Inscriptions
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
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                                <i data-feather="book"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Total Cours</h3>
                                <p class="text-2xl font-semibold"><?php echo $statistiques['total_cours']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i data-feather="users"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Total Étudiants</h3>
                                <p class="text-2xl font-semibold"><?php echo $statistiques['total_etudiants']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i data-feather="book-open"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Cours Inscrits</h3>
                                <p class="text-2xl font-semibold">
                                <?php echo $statistiques['total_cours_inscrits']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Top 3 des Cours les Plus Inscrits -->
                <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Top 3 des Cours les Plus Inscrits</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cours</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiants Inscrits</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($statistiques['top_cours'] as $cours): ?>
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <!-- Afficher l'image du cours -->
                                                <img src="../../assets/uploads/<?php echo htmlspecialchars($cours['image']); ?>" alt="Course" class="w-10 h-10 rounded">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($cours['titre']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                <?php echo htmlspecialchars($cours['categorie']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($cours['nb_inscriptions']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Actif
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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