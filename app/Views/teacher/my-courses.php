<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Role.php';
require_once '../../classes/Enseignant.php';

// Vérifier si l'utilisateur est un enseignant
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Enseignant') {
    header('Location: ../../public/login.php');
    exit();
}

// Vérifier si une requête de suppression a été envoyée via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_course'])) {
    $coursId = $_POST['id'] ?? null;

    if ($coursId) {
        // Instancier l'objet Enseignant
        $enseignant = new Enseignant(
            $_SESSION['user']['nom'],
            $_SESSION['user']['email'],
            '', 
            new Role(2, $_SESSION['user']['role']),
            $_SESSION['user']['status']
        );

        // Supprimer le cours
        try {
            $enseignant->supprimerCours((int)$coursId);
            $_SESSION['success'] = "Le cours a été supprimé avec succès.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de la suppression du cours : " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "ID du cours manquant.";
    }

    // Rediriger pour éviter la soumission multiple du formulaire
    header('Location: my-courses.php');
    exit();
}

$_SESSION['user']['role_id'] = 2;
$Enseignant = new Enseignant(
    $_SESSION['user']['nom'],
    $_SESSION['user']['email'],
    '', 
    new Role(2, $_SESSION['user']['role']),
    $_SESSION['user']['status']
);

// Récupérer les cours créés par l'enseignant
$courses = $Enseignant->listeCoursCrees();

// Grouper les cours par catégorie
$coursesByCategory = [];
foreach ($courses as $course) {
    $categoryId = $course['categorie_id'];
    if (!isset($coursesByCategory[$categoryId])) {
        $coursesByCategory[$categoryId] = [
            'nom' => $course['categorie_nom'],
            'cours' => []
        ];
    }
    $coursesByCategory[$categoryId]['cours'][] = $course;
}
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
                <a href="dashboard.php" class="flex items-center px-6 py-3 text-indigo-100 hover:bg-indigo-800">
                    <i data-feather="bar-chart-2" class="mr-3"></i>
                    Statistiques
                </a>
                <a href="manage-courses.php" class="flex items-center px-6 py-3 text-indigo-100 hover:bg-indigo-800">
                    <i data-feather="plus-circle" class="mr-3"></i>
                    Nouveau Cours
                </a>
                <a href="my-courses.php" class="flex items-center px-6 py-3 text-indigo-100 bg-indigo-800">
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
                    <h1 class="text-2xl font-bold text-gray-800">Mes Cours par Catégorie</h1>
                </div>
            </header>

            <main class="p-6">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['success']); ?></span>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['error']); ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php foreach ($coursesByCategory as $categoryId => $category): ?>
                    <div class="mb-8">
                        <!-- Titre de la catégorie -->
                        <h2 class="text-xl font-bold mb-4"><?php echo htmlspecialchars($category['nom']); ?></h2>

                        <!-- Grille de cours pour cette catégorie -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($category['cours'] as $course): ?>
                                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                    <!-- Image du cours -->
                                    <img src="../../assets/uploads/<?php echo htmlspecialchars($course['image']); ?>" alt="<?php echo htmlspecialchars($course['titre']); ?>" class="w-full h-48 object-cover">

                                    <!-- Contenu de la carte -->
                                    <div class="p-6">
                                        <!-- Titre du cours -->
                                        <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($course['titre']); ?></h3>

                                        <!-- Description du cours -->
                                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($course['description']); ?></p>

                                        <!-- Type du cours -->
                                        <div class="flex items-center mb-4">
                                            <span class="inline-block bg-indigo-100 text-indigo-800 px-3 py-1 text-sm font-semibold rounded-full">
                                                <?php echo htmlspecialchars($course['type']); ?>
                                            </span>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex justify-between items-center">
                                            <a href="edit-course.php?id=<?php echo htmlspecialchars($course['id']); ?>" class="text-indigo-600 hover:text-indigo-900">
                                                <i data-feather="edit" class="w-5 h-5"></i>
                                            </a>
                                            <!-- Formulaire de suppression -->
                                            <form action="my-courses.php" method="POST" >
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($course['id']); ?>">
                                                <input type="hidden" name="delete_course" value="1">
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i data-feather="trash-2" class="w-5 h-5"></i>
                                                </button>
                                            </form>
                                            <a href="" class="text-green-600 hover:text-green-900 flex items-center">
                                                <i data-feather="users" class="w-5 h-5 mr-1"></i>
                                                <span class="text-sm"><?php echo htmlspecialchars($course['nb_etudiants']); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </main>
        </div>
    </div>

    <script>
        // Initialize Feather Icons
        feather.replace();
    </script>
</body>
</html>