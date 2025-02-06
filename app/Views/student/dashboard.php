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
$Etudiant_id = $_SESSION['user']['id'];
$Etudiant = new Etudiant(
    $_SESSION['user']['nom'],
    $_SESSION['user']['email'],
    '',
    new Role(3, $_SESSION['user']['role']),
    $_SESSION['user']['status']
);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cours_id'])) {
    $cours_id = (int)$_POST['cours_id'];

    if (!$cours_id) {
        $_SESSION['error'] = "ID du cours invalide.";
        header('Location: dashboard.php');
        exit();
    }

    try {
        $Etudiant->sInscrireAuCours($Etudiant_id, $cours_id);
        $_SESSION['message'] = "Vous êtes inscrit au cours avec succès.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur lors de l'inscription : " . $e->getMessage();
    }

    header('Location: dashboard.php');
    exit();
}

// Connexion à la base de données
$db = Database::getInstance();
$conn = $db->getConnection();

// Récupérer les catégories
$categories_query = "SELECT * FROM categorie";
$categories_stmt = $conn->query($categories_query);
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination et filtrage par catégorie
$limit = 6; // Nombre de cours par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Récupérer les cours pour la page actuelle
$courses = $Etudiant->listeCours($limit, $page, $category_id);

// Récupérer le nombre total de cours pour la pagination
$total_query = "
    SELECT COUNT(*) as total 
    FROM cours
    JOIN enseignant_cours ON cours.id = enseignant_cours.id_cours
    WHERE cours.status = 1
";
if ($category_id) {
    $total_query .= " AND cours.categorie_id = :category_id";
}
$total_stmt = $conn->prepare($total_query);
if ($category_id) {
    $total_stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
}
$total_stmt->execute();
$total_row = $total_stmt->fetch(PDO::FETCH_ASSOC);
$total_courses = $total_row['total'];
$total_pages = ceil($total_courses / $limit);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Étudiant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.1/feather.min.js"></script>
    <style>
        /* Style personnalisé pour les flèches de navigation */
        .scroll-button {
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }
        .scroll-button:hover {
            background-color: #f3f4f6;
            transform: scale(1.1);
        }
        .scroll-button:active {
            transform: scale(0.9);
        }
        /* Masquer la barre de défilement */
        .category-filters {
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
            overflow-x: auto; /* Activer le défilement horizontal */
            scroll-behavior: smooth; /* Défilement fluide */
            display: flex;
            gap: 12px; /* Espace entre les boutons */
        }
        .category-filters::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
        /* Limiter la largeur du conteneur des catégories */
        .category-container {
            width: 100%;
            max-width: 800px; /* Ajustez selon vos besoins */
            overflow: hidden;
            position: relative;
        }
    </style>
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
                <a href="dashboard.php" class="flex items-center px-6 py-3 text-blue-100 bg-blue-700">
                    <i data-feather="home" class="mr-3"></i>
                    Accueil
                </a>
                <a href="MesCours.php" class="flex items-center px-6 py-3 text-blue-100 hover:bg-blue-700">
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
                        <h1 class="text-2xl font-bold text-gray-800">Catalogue des Cours</h1>
                        <div class="relative">
                            <input type="text" placeholder="Rechercher un cours..." 
                                class="w-96 px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <i data-feather="search" class="absolute right-3 top-2.5 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </header>

            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $_SESSION['message']; ?></span>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $_SESSION['error']; ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <!-- Category Filters -->
                <div class="flex items-center space-x-4 mb-8">
                    <!-- Bouton de défilement vers la gauche -->
                    <div class="scroll-button" onclick="scrollCategories(-1)">
                        <i data-feather="chevron-left" class="w-4 h-4 text-gray-700"></i>
                    </div>

                    <!-- Conteneur des catégories -->
                    <div class="category-container">
                        <div class="category-filters" id="categoryFilters">
                            <!-- Bouton "Tous les cours" -->
                            <a href="?page=1" class="px-4 py-2 text-sm font-medium rounded-full transition-all duration-300 whitespace-nowrap 
                                <?php echo !$category_id ? 'bg-blue-600 text-white shadow-lg hover:bg-blue-700' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'; ?>">
                                Tous les cours
                            </a>

                            <!-- Boutons des catégories -->
                            <?php foreach ($categories as $category): ?>
                                <a href="?page=1&category=<?php echo $category['id']; ?>" class="px-4 py-2 text-sm font-medium rounded-full transition-all duration-300 whitespace-nowrap 
                                    <?php echo $category_id == $category['id'] ? 'bg-blue-600 text-white shadow-lg hover:bg-blue-700' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Bouton de défilement vers la droite -->
                    <div class="scroll-button" onclick="scrollCategories(1)">
                        <i data-feather="chevron-right" class="w-4 h-4 text-gray-700"></i>
                    </div>
                </div>

                <!-- Course Catalog -->
                <section>
                    <h2 class="text-xl font-semibold mb-6">Cours Recommandés</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($courses as $course): ?>
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                <div class="relative pb-60">
                                    <img src="../../assets/uploads/<?php echo $course['cours_image']; ?>" alt="Course thumbnail" class="absolute h-full w-full object-cover">
                                    <button class="absolute top-4 right-4 p-2 bg-white rounded-full shadow hover:bg-gray-100">
                                        <i data-feather="heart" class="w-4 h-4 text-gray-600"></i>
                                    </button>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center mb-2">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full"><?php echo $course['cours_type']; ?></span>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-2"><?php echo $course['cours_titre']; ?></h3>
                                    <p class="text-gray-600 text-sm mb-4"><?php echo $course['cours_description']; ?></p>
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <i data-feather="user" class="w-4 h-4 text-gray-600"></i>
                                            <span class="ml-2 text-sm text-gray-600">Dr.<?php echo $course['enseignant_nom']; ?></span>
                                        </div>
                                        <div class="flex items-center text-yellow-400">
                                            <i data-feather="star" class="w-4 h-4 fill-current"></i>
                                            <span class="ml-1 text-sm">4.9</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t">
                                        <div class="flex justify-between items-center">
                                            <div class="text-lg font-bold text-gray-900">
                                                199.99 DH
                                            </div>
                                            <form action="" method="POST" class="inline">
                                                <input type="hidden" name="cours_id" value="<?php echo $course['cours_id']; ?>">
                                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                    S'inscrire
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Pagination -->
                <div class="flex justify-center mt-8">
                    <nav class="inline-flex rounded-md shadow-sm">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>" class="px-4 py-2 bg-blue-600 text-white rounded-l-md hover:bg-blue-700">
                                Précédent
                            </a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-100 <?php echo $i == $page ? 'bg-blue-100 text-blue-700' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>" class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700">
                                Suivant
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Initialize Feather Icons
        feather.replace();

        // Fonction pour faire défiler les catégories
        function scrollCategories(direction) {
            const container = document.getElementById('categoryFilters');
            const scrollAmount = 200; // Montant de défilement en pixels
            container.scrollBy({
                left: direction * scrollAmount,
                behavior: 'smooth'
            });
        }
    </script>
</body>
</html>