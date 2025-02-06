<?php
    session_start();
    require_once '../classes/Database.php';
    require_once '../classes/Administrateur.php';

    // Connexion à la base de données
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Récupérer les catégories
    $categories_query = "SELECT * FROM categorie";
    $categories_stmt = $conn->query($categories_query);
    $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pagination et filtrage par catégorie
    $limit = 6; 
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
    $search = isset($_GET['search']) ? $_GET['search'] : null;

    // Récupérer les cours pour la page actuelle
    $administrateur = new Administrateur('Admin', 'admin@admin.com', 'password', new Role(1, 'Administrateur'));
    $courses = $administrateur->listeCours($limit, $page, $category_id, $search);

    // Récupérer le nombre total de cours pour la pagination
    $total_courses = $administrateur->countCours($category_id, $search);
    $total_pages = ceil($total_courses / $limit);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Courses - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-gray-50 to-white min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-gray-900 border-b border-gray-800">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <a href="/" class="flex items-center space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                    <span class="text-2xl font-bold text-white">Youdemy</span>
                </a>

                <div class="flex items-center space-x-6">
                    <a href="course.php" class="text-white font-medium transition-colors">
                        Courses
                    </a>
                    <a href="login.php" class="text-gray-300 hover:text-white font-medium transition-colors">
                        Login
                    </a>
                    <a href="register.php" class="bg-white text-gray-900 px-6 py-3 rounded-full hover:bg-gray-100 transition-colors font-medium">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-12">
        <!-- Header -->
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Browse Our Courses</h1>

            <!-- Barre de recherche -->
            <div class="mb-6">
                <form action="" method="GET">
                    <div class="relative w-full max-w-md">
                        <input
                            type="text"
                            name="search"
                            placeholder="Search courses..."
                            class="w-full px-4 py-2 rounded-full bg-gray-100 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-colors"
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                        />
                        <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Boutons de filtrage -->
            <div class="flex flex-wrap gap-4">
                <a href="?page=1" class="px-4 py-2 rounded-full <?php echo !$category_id ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'; ?> hover:bg-indigo-700 transition-colors">All Courses</a>
                <?php foreach ($categories as $category): ?>
                    <a href="?page=1&category=<?php echo $category['id']; ?>" class="px-4 py-2 rounded-full <?php echo $category_id == $category['id'] ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'; ?> hover:bg-gray-300 transition-colors">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Course Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($courses as $course): ?>
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <img src="../assets/uploads/<?php echo $course['cours_image']; ?>" alt="Course thumbnail" class="w-full h-48 object-cover"/>
                    <div class="p-6">
                        <span class="text-sm font-medium text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full"><?php echo htmlspecialchars($course['categorie_name']); ?></span>
                        <h3 class="text-xl font-semibold mt-4 mb-2"><?php echo htmlspecialchars($course['cours_titre']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($course['cours_description']); ?></p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-900">199.99DH</span>
                            <button 
                                onclick="showEnrollAlert()" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700 transition-colors"
                            >
                                Enroll Now
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-8">
            <nav class="inline-flex rounded-md shadow-sm">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . $search : ''; ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-l-md hover:bg-indigo-700 transition-colors">Previous</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . $search : ''; ?>" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-100 <?php echo $i == $page ? 'bg-indigo-100 text-indigo-700' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . $search : ''; ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 transition-colors">Next</a>
                <?php endif; ?>
            </nav>
        </div>
    </main>

    <!-- Footer -->
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

    <script>
        function showEnrollAlert() {
            Swal.fire({
                title: 'Inscription requise',
                text: 'Vous devez d\'abord vous inscrire.',
                icon: 'warning',
                confirmButtonText: 'Se connecter',
                showCancelButton: true,
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        }
    </script>
</body>
</html>