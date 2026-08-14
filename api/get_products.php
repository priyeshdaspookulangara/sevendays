<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db_connect.php';

$ids_str = $_GET['ids'] ?? '';
if (!is_string($ids_str) || empty($ids_str)) {
    echo json_encode([]);
    exit;
}

$ids = explode(',', $ids_str);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll();

echo json_encode($products);
?>