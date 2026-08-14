<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// MySQL Database Configuration defaults
$host = 'localhost';
$port = '3306';
$db   = 'ecommerce';
$user = 'root';
$pass = ''; // Default password is often empty in local environments
$charset = 'utf8mb4';

// Check for DATABASE_URL or other standard DB URLs (Heroku, Render, JawsDB, ClearDB, etc.)
$db_url = getenv('DATABASE_URL') ?: getenv('JAWSDB_URL') ?: getenv('CLEARDB_DATABASE_URL');
if ($db_url) {
    $parsed_url = parse_url($db_url);
    if ($parsed_url) {
        $host = $parsed_url['host'] ?? $host;
        $port = $parsed_url['port'] ?? $port;
        $user = $parsed_url['user'] ?? $user;
        $pass = $parsed_url['pass'] ?? $pass;
        $db   = isset($parsed_url['path']) ? ltrim($parsed_url['path'], '/') : $db;
    }
} else {
    // Check for individual environment variables
    $host = getenv('DB_HOST') ?: $host;
    $port = getenv('DB_PORT') ?: $port;
    $db   = getenv('DB_NAME') ?: getenv('DB_DATABASE') ?: $db;
    $user = getenv('DB_USER') ?: getenv('DB_USERNAME') ?: $user;
    $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : $pass);
}

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // If the database connection fails, set 500 HTTP response code and show a clean message
     http_response_code(500);
     die("Database connection failed. Please ensure MySQL is running and the database exists with the correct credentials.\nError: " . $e->getMessage());
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