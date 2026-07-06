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
     // Log the error for the administrator
     error_log("Database connection failed: " . $e->getMessage());

     // Return a clean error message to the user
     http_response_code(500);
     die("The website is currently experiencing technical difficulties. Please try again later.");
}

// Common functions
function get_all_products($pdo) {
    try {
        $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id");
        return $stmt->fetchAll();
    } catch (\PDOException $e) {
        error_log("Error fetching products: " . $e->getMessage());
        return [];
    }
}

function get_categories($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM categories");
        return $stmt->fetchAll();
    } catch (\PDOException $e) {
        error_log("Error fetching categories: " . $e->getMessage());
        return [];
    }
}

function get_featured_products($pdo) {
    try {
        $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1");
        return $stmt->fetchAll();
    } catch (\PDOException $e) {
        error_log("Error fetching featured products: " . $e->getMessage());
        return [];
    }
}

function get_star_product($pdo) {
    try {
        $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_star = 1 LIMIT 1");
        return $stmt->fetch();
    } catch (\PDOException $e) {
        error_log("Error fetching star product: " . $e->getMessage());
        return null;
    }
}

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>