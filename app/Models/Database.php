<?php
class Database {
    private static $instance = null;
    private $pdo;

    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct($dsn, $username, $password) {
        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Erreur de connexion à la base de données : " . $e->getMessage());
            throw new Exception("Échec de la connexion à la base de données.");
        }
    }

    // Méthode pour obtenir l'instance unique de la base de données
    public static function getInstance($dsn = null, $username = null, $password = null) {
        if (self::$instance === null) {
            $dsn = $dsn ?? 'pgsql:host=localhost;port=5432;dbname=youdemy_mvc'; 
            $username = $username ?? 'postgres'; 
            $password = $password ?? 'ADMIN'; 
            self::$instance = new Database($dsn, $username, $password);
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}

// try {
//     $db = Database::getInstance();
//     $pdo = $db->getConnection();
//     echo "✅ Connection to the database was successful!";
// } catch (Exception $e) {
//     echo "❌ Connection failed: " . $e->getMessage();
// }
?>
