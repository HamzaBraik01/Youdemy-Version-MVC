<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Etudiant.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Etudiant') {
    header('Location: ../../public/login.php');
    exit();
}

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$course_id) {
    $_SESSION['error'] = "ID du cours invalide.";
    header('Location: MesCours.php');
    exit();
}

$Etudiant = new Etudiant(
    $_SESSION['user']['nom'],
    $_SESSION['user']['email'],
    '',
    new Role(3, $_SESSION['user']['role']),
    $_SESSION['user']['status']
);

try {
    $courseDetails = $Etudiant->showCours($course_id);
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: MesCours.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Cours - <?php echo htmlspecialchars($courseDetails['course']['cours_titre']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.1/feather.min.js"></script>
</head>
<body class="bg-gray-100">
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

    <!-- En-tête -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-4">
                <!-- Bouton de retour -->
                <button onclick="history.back()" class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                    <i data-feather="arrow-left" class="w-6 h-6"></i>
                    <span class="ml-2">Retour</span>
                </button>
                <h1 class="text-2xl font-bold text-gray-800">Détails du Cours</h1>
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Section principale du cours -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="relative h-64 rounded-lg overflow-hidden shadow-lg">
                    <img src="../../assets/uploads/<?php echo htmlspecialchars($courseDetails['course']['cours_image']); ?>" alt="Course thumbnail" class="absolute h-full w-full object-cover">
                </div>

                <!-- Titre et description -->
                <div class="mt-6 bg-white p-6 rounded-lg shadow-sm">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($courseDetails['course']['cours_titre']); ?></h2>
                    <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($courseDetails['course']['cours_description']); ?></p>

                    <!-- Informations supplémentaires -->
                    <div class="flex items-center space-x-4 text-gray-600">
                        <div class="flex items-center">
                            <i data-feather="user" class="w-5 h-5 mr-2"></i>
                            <span>Enseignant : <?php echo htmlspecialchars($courseDetails['course']['enseignant_nom']); ?></span>
                        </div>
                        <div class="flex items-center">
                            <i data-feather="book" class="w-5 h-5 mr-2"></i>
                            <span>Catégorie : <?php echo htmlspecialchars($courseDetails['course']['categorie_name']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Contenu du cours -->
                <div class="mt-6 bg-white p-6 rounded-lg shadow-sm">
                    <?php if ($courseDetails['course']['cours_type'] === 'CONTEXTE'): ?>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Objectif du Cours</h3>
                        <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($courseDetails['course']['contexte_objectif']); ?></p>

                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Contenu du Cours</h3>
                        <div class="prose max-w-none">
                            <?php echo $courseDetails['course']['cours_contenu']; ?>
                        </div>
                    <?php elseif ($courseDetails['course']['cours_type'] === 'VIDEO'): ?>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Vidéo du Cours</h3>
                        <div class="relative aspect-video rounded-lg overflow-hidden shadow-lg">
                            <video controls class="w-full h-full">
                                <source src="../../assets/uploads/<?php echo htmlspecialchars($courseDetails['course']['video_url']); ?>" type="video/mp4">
                                Votre navigateur ne supporte pas la lecture de vidéos.
                            </video>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Colonne de droite : Informations supplémentaires -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Informations du Cours</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center text-gray-600">
                            <i data-feather="clock" class="w-5 h-5 mr-2"></i>
                            <span>Durée : 5 heures</span>
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i data-feather="bar-chart" class="w-5 h-5 mr-2"></i>
                            <span>Niveau : Débutant</span>
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i data-feather="file-text" class="w-5 h-5 mr-2"></i>
                            <span>Ressources : 10 fichiers</span>
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i data-feather="award" class="w-5 h-5 mr-2"></i>
                            <span>Certificat : Oui</span>
                        </li>
                    </ul>
                </div>
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
                <p class="text-gray-500 text-sm">© 2025 Youdemy. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Script pour les icônes Feather -->
    <script>
        feather.replace();
    </script>
</body>
</html>