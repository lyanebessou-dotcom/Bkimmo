<?php
/**
 * Configuration de la base de données pour Infinity Free
 * 
 * Instructions pour Infinity Free:
 * 1. Connectez-vous à votre panel Infinity Free
 * 2. Allez dans "MySQL Databases"
 * 3. Créez une base de données et notez les informations
 * 4. Remplacez les valeurs ci-dessous par vos informations réelles
 */

// Configuration de la base de données
define('DB_HOST', 'sqlxxx.infinityfree.com'); // Remplacez sqlxxx par votre serveur MySQL
define('DB_USER', 'if0_xxxxxxxx'); // Remplacez par votre nom d'utilisateur MySQL
define('DB_PASS', 'votre_mot_de_passe'); // Remplacez par votre mot de passe MySQL
define('DB_NAME', 'if0_xxxxxxxx_logiloc_immo'); // Remplacez par le nom de votre base de données

// Configuration du site
define('SITE_URL', 'https://votre-site.infinityfreeapp.com'); // Remplacez par votre URL de site
define('SITE_NAME', 'Logiloc Immo');

// Configuration de session
define('SESSION_DURATION', 3600); // 1 heure en secondes

// Configuration des uploads
define('MAX_FILE_SIZE', 5242880); // 5MB en octets
define('UPLOAD_DIR', __DIR__ . '/images/uploads/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Mode de développement (false en production)
define('DEBUG_MODE', false);

// Rapport d'erreurs
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Fuseau horaire
date_default_timezone_set('Europe/Paris');

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Connexion à la base de données
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        } else {
            die("Erreur de connexion à la base de données. Veuillez contacter l'administrateur.");
        }
    }
}

/**
 * Fonction de nettoyage des entrées
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Vérification si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vérification si l'utilisateur est admin
 */
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

/**
 * Redirection sécurisée
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Message flash
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
