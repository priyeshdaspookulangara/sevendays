<?php
// MySQL Database Configuration
$host = 'localhost';
$db   = 'ecommerce';
$user = 'root';
$pass = ''; // Default password is often empty in local environments
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // If the database doesn't exist, we might want to handle it or just fail
     // For this task, we assume the user has set up the database.
     die("Database connection failed. Please ensure MySQL is running and the 'ecommerce' database exists with the correct credentials.\nError: " . $e->getMessage());
}

// Common functions
function get_all_products($pdo) {
    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id");
    return $stmt->fetchAll();
}

function get_categories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories");
    return $stmt->fetchAll();
}

function get_featured_products($pdo) {
    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1");
    return $stmt->fetchAll();
}

function get_star_product($pdo) {
    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_star = 1 LIMIT 1");
    return $stmt->fetch();
}

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>