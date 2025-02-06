<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Role.php';
require_once '../../classes/Enseignant.php';
require_once '../../classes/Video.php';
require_once '../../classes/Context.php';

// Vérifier si l'utilisateur est connecté et est un enseignant
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Enseignant') {
    header('Location: ../../public/login.php');
    exit();
}

// Initialiser l'objet Enseignant
$_SESSION['user']['role_id'] = 2;
$Enseignant = new Enseignant(
    $_SESSION['user']['nom'],
    $_SESSION['user']['email'],
    '',
    new Role(2, $_SESSION['user']['role']),
    $_SESSION['user']['status']
);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $title = $_POST['title'];
    $description = $_POST['description'];
    $content = $_POST['content'];
    $image = $_FILES['image']['name'];
    $type = $_POST['contentType'];
    $status = 1; // Par défaut, le cours est actif
    $categorie_id = $_POST['category']; // Récupérer la catégorie sélectionnée
    $tags = $_POST['tags'] ?? [];

    // Gestion de l'upload de l'image
    $target_dir = "../../assets/uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    // Données supplémentaires en fonction du type de contenu
    $additionalData = [];
    if ($type === 'VIDEO') {
        $additionalData['url'] = $_FILES['video']['name'];
        // Gestion de l'upload de la vidéo
        $target_file_video = $target_dir . basename($_FILES["video"]["name"]);
        move_uploaded_file($_FILES["video"]["tmp_name"], $target_file_video);
    } elseif ($type === 'CONTEXTE') {
        // Vérifier si le champ objectif est défini
        if (isset($_POST['objectif'])) {
            $additionalData['objectif'] = $_POST['objectif'];
        } else {
            $additionalData['objectif'] = ''; // Valeur par défaut si le champ n'est pas défini
        }
    }

    // Ajouter le cours
    try { 
        $Enseignant->ajouterCours($title, $description, $content, $image, $type, $status, $categorie_id, $tags, $additionalData);
        $success_message = "Le cours a été ajouté avec succès.";
    } catch (Exception $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
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
    <style>
        [contenteditable="true"]:focus {
            outline: none;
        }
        .form-container {
            background-color: #f9fafb;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
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
                <a href="manage-courses.php" class="flex items-center px-6 py-3 text-indigo-100 bg-indigo-800">
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
                    <h1 class="text-2xl font-bold text-gray-800">Nouveau Cours</h1>
                </div>
            </header>

            <main class="p-6">
                <!-- Afficher les messages de succès ou d'erreur -->
                <?php if (isset($success_message)) : ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($error_message)) : ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <div class="max-w-4xl mx-auto form-container">
                    <form id="courseForm" method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                        <!-- Titre du cours -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i data-feather="book" class="mr-2"></i>
                                Titre du cours
                            </label>
                            <input type="text" id="title" name="title" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i data-feather="align-left" class="mr-2"></i>
                                Description
                            </label>
                            <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
                        </div>

                        <!-- Catégorie -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i data-feather="folder" class="mr-2"></i>
                                Catégorie
                            </label>
                            <div class="flex gap-4">
                                <select id="category" name="category" class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    <?php
                                    // Récupérer les catégories depuis la base de données
                                    $db = Database::getInstance()->getConnection();
                                    $query = "SELECT * FROM categorie";
                                    $stmt = $db->query($query);
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Image du cours -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i data-feather="image" class="mr-2"></i>
                                Image du cours
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*" required>
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Télécharger une image</span>
                                    </label>
                                    <p class="text-xs text-gray-500">PNG, JPG jusqu'à 10MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Type de contenu -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3 flex items-center">
                                <i data-feather="type" class="mr-2"></i>
                                Type de contenu
                            </label>
                            <div class="flex gap-4 mb-4">
                                <label class="flex items-center">
                                    <input type="radio" name="contentType" value="VIDEO" class="form-radio h-4 w-4 text-indigo-600" required>
                                    <span class="ml-2">Vidéo</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="contentType" value="CONTEXTE" class="form-radio h-4 w-4 text-indigo-600">
                                    <span class="ml-2">Contenu textuel</span>
                                </label>
                            </div>

                            <!-- Zone vidéo -->
                            <div id="videoUpload" class="hidden">
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <input id="video" name="video" type="file" class="sr-only" accept="video/*">
                                        <label for="video" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Télécharger une vidéo</span>
                                        </label>
                                        <p class="text-xs text-gray-500">MP4, WebM jusqu'à 1GB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Éditeur de texte -->
                            <div id="textEditor" class="hidden">
                                <div class="border border-gray-300 rounded-md">
                                    <div class="bg-gray-50 p-2 border-b border-gray-300 flex flex-wrap gap-2">
                                        <button type="button" class="p-2 hover:bg-gray-200 rounded" onclick="execCommand('bold')">
                                            <i data-feather="bold"></i>
                                        </button>
                                        <button type="button" class="p-2 hover:bg-gray-200 rounded" onclick="execCommand('italic')">
                                            <i data-feather="italic"></i>
                                        </button>
                                        <button type="button" class="p-2 hover:bg-gray-200 rounded" onclick="execCommand('underline')">
                                            <i data-feather="underline"></i>
                                        </button>
                                        <button type="button" class="p-2 hover:bg-gray-200 rounded" onclick="execCommand('insertOrderedList')">
                                            <i data-feather="list"></i>
                                        </button>
                                        <button type="button" class="p-2 hover:bg-gray-200 rounded" onclick="execCommand('insertUnorderedList')">
                                            <i data-feather="list"></i>
                                        </button>
                                    </div>
                                    <div contenteditable="true" id="editor" class="p-4 min-h-[200px]"></div>
                                    <input type="hidden" name="content" id="hiddenContent">
                                </div>
                                <!-- Ajouter le champ pour l'objectif -->
                                <div class="mt-4">
                                    <label for="objectif" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                        <i data-feather="target" class="mr-2"></i>
                                        Objectif du cours
                                    </label>
                                    <textarea id="objectif" name="objectif" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <i data-feather="tag" class="mr-2"></i>
                                Tags
                            </label>
                            <div class="flex flex-wrap gap-4 mb-2">
                                <?php
                                // Récupérer les tags depuis la base de données
                                $query = "SELECT * FROM tag";
                                $stmt = $db->query($query);
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="tags[]" value="' . $row['id'] . '" class="form-checkbox h-5 w-5 text-indigo-600 rounded">
                                        <span class="text-gray-700">' . htmlspecialchars($row['name']) . '</span>
                                    </label>';
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Bouton de soumission -->
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Créer le cours
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Initialiser les icônes Feather
        feather.replace();

        // Gestion du type de contenu
        document.querySelectorAll('input[name="contentType"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const videoUpload = document.getElementById('videoUpload');
                const textEditor = document.getElementById('textEditor');
                const videoInput = document.getElementById('video');
                
                if (this.value === 'VIDEO') {
                    videoUpload.classList.remove('hidden');
                    textEditor.classList.add('hidden');
                    videoInput.required = true;
                    document.getElementById('editor').textContent = '';
                } else {
                    videoUpload.classList.add('hidden');
                    textEditor.classList.remove('hidden');
                    videoInput.required = false;
                }
            });
        });

        // Fonctions de l'éditeur de texte
        function execCommand(command) {
            document.execCommand(command, false, null);
            document.getElementById('editor').focus();
        }

        // Gestion de la soumission du formulaire
        document.getElementById('courseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Sauvegarder le contenu de l'éditeur dans le champ caché
            const editorContent = document.getElementById('editor').innerHTML;
            document.getElementById('hiddenContent').value = editorContent;

            // Validation supplémentaire
            const contentType = document.querySelector('input[name="contentType"]:checked');
            if (!contentType) {
                alert('Veuillez sélectionner un type de contenu');
                return;
            }

            if (contentType.value === 'VIDEO' && !document.getElementById('video').files.length) {
                alert('Veuillez sélectionner une vidéo');
                return;
            }

            if (contentType.value === 'CONTEXTE') {
                const objectif = document.getElementById('objectif').value.trim();
                if (!objectif) {
                    alert('Veuillez ajouter un objectif pour le cours');
                    return;
                }
                if (!editorContent.trim()) {
                    alert('Veuillez ajouter du contenu textuel');
                    return;
                }
            }

            // Soumettre le formulaire
            this.submit();
        });

        // Vérification de la taille des fichiers
        document.getElementById('video').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.size > 1024 * 1024 * 1024) { // 1GB
                alert('La taille de la vidéo ne doit pas dépasser 1GB');
                this.value = '';
            }
        });

        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.size > 10 * 1024 * 1024) { // 10MB
                alert('La taille de l\'image ne doit pas dépasser 10MB');
                this.value = '';
            }
        });
    </script>
</body>
</html>