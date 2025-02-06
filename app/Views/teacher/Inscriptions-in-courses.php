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

$etudiantsInscrits = $Enseignant->getEtudiantsInscrits();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscriptions-in-courses</title>
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
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Étudiants inscrits à vos cours</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-sm font-semibold text-gray-600">Nom</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-sm font-semibold text-gray-600">Email</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-sm font-semibold text-gray-600">Cours</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($etudiantsInscrits)): ?>
                                    <?php foreach ($etudiantsInscrits as $etudiant): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-2 px-4 border-b border-gray-200 text-sm text-gray-700"><?php echo htmlspecialchars($etudiant['nom']); ?></td>
                                            <td class="py-2 px-4 border-b border-gray-200 text-sm text-gray-700"><?php echo htmlspecialchars($etudiant['email']); ?></td>
                                            <td class="py-2 px-4 border-b border-gray-200 text-sm text-gray-700"><?php echo htmlspecialchars($etudiant['cours_titre']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="py-2 px-4 border-b border-gray-200 text-sm text-gray-700 text-center">Aucun étudiant inscrit à vos cours.</td>
                                    </tr>
                                <?php endif; ?>
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