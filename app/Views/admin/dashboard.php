<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Administrateur.php';
require_once '../../classes/Role.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Administrateur') {
    header('Location: ../../public/login.php');
    exit();
}
$_SESSION['user']['role_id']=1;
$admin = new Administrateur(
    $_SESSION['user']['nom'],
    $_SESSION['user']['email'],
    '', 
    new Role(1, $_SESSION['user']['role']),
    $_SESSION['user']['status']
);

$statistiques = $admin->consulterStatistiquesGlobales();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.1/feather.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 flex flex-col">
            <div class="p-5">
                <h2 class="text-2xl font-bold">Youdemy</h2>
            </div>
            <nav class="flex-1">
                <a href="dashboard.php" class="flex items-center px-6 py-3 text-gray-300 bg-gray-700">
                    <i data-feather="bar-chart-2" class="mr-3"></i>
                    Statistiques
                </a>
                <a href="Validation_Comptes.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700">
                    <i data-feather="users" class="mr-3"></i>
                    Validation Comptes
                </a>
                <a href="manage-users.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700">
                    <i data-feather="user-check" class="mr-3"></i>
                    Gestion Utilisateurs
                </a>
                <a href="content.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700">
                    <i data-feather="book" class="mr-3"></i>
                    Gestion Contenus
                </a>
                <a href="tagManager.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700">
                    <i data-feather="tag" class="mr-3"></i>
                    Gestion Tags
                </a>
            </nav>
            <!-- Déconnexion -->
            <div class="p-5 border-t border-gray-700">
                <a href="../../assets/php/logout.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700">
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
                    <div class="text-gray-600">
                        Bienvenue, <?php echo htmlspecialchars($_SESSION['user']['nom']); ?>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                                <i data-feather="book"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Total Cours</h3>
                                <p class="text-2xl font-semibold"><?php echo $statistiques['totalCours']; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-500">
                                <i data-feather="users"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Total Étudiants</h3>
                                <p class="text-2xl font-semibold"><?php echo $statistiques['totalEtudiants']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                                <i data-feather="user-check"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Enseignants</h3>
                                <p class="text-2xl font-semibold"><?php echo $statistiques['totalEnseignants']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                                <i data-feather="tag"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Total Tags</h3>
                                <p class="text-2xl font-semibold"><?php echo $statistiques['totalTags']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Répartition par Catégorie</h3>
                        <canvas id="categoryChart" height="200"></canvas>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Top 3 Enseignants</h3>
                        <div class="space-y-4" id="topTeachersContainer">
                            <?php foreach ($statistiques['topEnseignants'] as $index => $enseignant): ?>
                            <div class="flex items-center">
                                <i data-feather="user" class="w-10 h-10 text-gray-700"></i>
                                <div class="ml-4 flex-1">
                                    <h4 class="font-semibold"><?php echo htmlspecialchars($enseignant['nom']); ?></h4>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo ($enseignant['total_cours'] / $statistiques['totalCours'] * 100); ?>%"></div>
                                    </div>
                                </div>
                                <span class="ml-4 text-sm font-semibold"><?php echo $enseignant['total_cours']; ?> cours</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Course Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold">Cours Populaires</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Cours</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Enseignant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Étudiants</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Catégorie</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($statistiques['repartitionCours'] as $cours): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($cours['name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($cours['enseignant']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?php echo $cours['total']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($cours['categorie']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $cours['status'] === 'Actif' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                <?php echo htmlspecialchars($cours['status']); ?>
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

        // Initialize Charts with dynamic data
        const categoryData = <?php echo json_encode($statistiques['repartitionCours']); ?>;
        const ctx = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(item => item.name),
                datasets: [{
                    data: categoryData.map(item => item.total),
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>