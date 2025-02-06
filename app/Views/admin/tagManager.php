<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Administrateur.php';
require_once '../../classes/Role.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Administrateur') {
    header('Location: ../../public/login.php');
    exit();
}
$_SESSION['user']['role_id'] = 1;
$admin = new Administrateur(
    $_SESSION['user']['nom'],
    $_SESSION['user']['email'],
    '',
    new Role(1, $_SESSION['user']['role']),
    $_SESSION['user']['status']
);

// Traitement du formulaire d'insertion de tags
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tags'])) {
    $tags = explode(',', $_POST['tags']);
    $tags = array_map('trim', $tags); // Nettoyer les tags
    $tags = array_filter($tags); // Supprimer les tags vides

    if (!empty($tags)) {
        $result = $admin->insererTagsEnMasse($tags);
        $message = '';
        if (!empty($result['insertedTags'])) {
            $message .= 'Tags insérés avec succès: ' . implode(', ', $result['insertedTags']) . '<br>';
        }
        if (!empty($result['duplicateTags'])) {
            $message .= 'Tags déjà existants: ' . implode(', ', $result['duplicateTags']);
        }
    } else {
        $message = '<p class="text-red-500">Veuillez entrer au moins un tag.</p>';
    }
}

// Traitement de la suppression d'un tag
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteTag'])) {
    $tagId = intval($_POST['tagId']);
    $db = Database::getInstance()->getConnection();

    // Supprimer le tag
    $stmt = $db->prepare("DELETE FROM Tag WHERE id = :id");
    $stmt->bindParam(':id', $tagId);

    if ($stmt->execute()) {
        $message = '<p class="text-green-500">Tag supprimé avec succès.</p>';
    } else {
        $message = '<p class="text-red-500">Erreur lors de la suppression du tag.</p>';
    }
}

// Récupérer tous les tags existants
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT * FROM Tag");
$tagsExistants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.1/feather.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 flex flex-col">
            <div class="p-5">
                <h2 class="text-2xl font-bold">Youdemy</h2>
            </div>
            <nav class="flex-1">
                <a href="dashboard.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700">
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
                <a href="tagManager.php" class="flex items-center px-6 py-3 text-gray-300 bg-gray-700">
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
                <!-- Formulaire d'insertion de tags -->
                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <h2 class="text-xl font-bold mb-4">Insertion en Masse de Tags</h2>
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label for="tagsInput" class="block text-sm font-medium text-gray-700">Tags (séparés par des virgules)</label>
                            <textarea id="tagsInput" name="tags" rows="4" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Insérer les Tags</button>
                    </form>
                    <?php if (isset($message)) : ?>
                        <div id="message" class="mt-4">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Affichage des tags existants -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-bold mb-4">Tags Existants</h2>
                    <div id="tagsList" class="flex flex-wrap gap-2">
                        <?php foreach ($tagsExistants as $tag) : ?>
                            <div class="bg-gray-200 px-3 py-1 rounded-full flex items-center">
                                <span><?php echo htmlspecialchars($tag['name']); ?></span>
                                <form method="POST" action="" class="inline">
                                    <input type="hidden" name="tagId" value="<?php echo $tag['id']; ?>">
                                    <button type="submit" name="deleteTag" class="ml-2 text-red-500 hover:text-red-700">
                                        <i data-feather="x"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
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