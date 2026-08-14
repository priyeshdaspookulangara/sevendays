<?php
header('Content-Type: application/json');
require_once __DIR__ . '/includes/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Ensure required fields and cart are set
$data['first_name'] = $data['first_name'] ?? '';
$data['last_name'] = $data['last_name'] ?? '';
$data['email'] = $data['email'] ?? '';
$data['phone'] = $data['phone'] ?? '';
$data['address'] = $data['address'] ?? '';
$data['cart'] = $data['cart'] ?? [];

if (!is_array($data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Cart must be an array']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Fetch actual prices from DB to prevent price manipulation
    $product_ids = array_column($data['cart'], 'id');
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $db_prices = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $total = 0;
    foreach ($data['cart'] as &$item) {
        if (!isset($db_prices[$item['id']])) {
            throw new Exception("Product not found: " . $item['id']);
        }
        $item['price'] = $db_prices[$item['id']];
        $total += $item['price'] * $item['quantity'];
    }

    $stmt = $pdo->prepare("INSERT INTO orders (first_name, last_name, email, phone, address, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['phone'],
        $data['address'],
        $total
    ]);

    $order_id = $pdo->lastInsertId();

    $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($data['cart'] as $item) {
        $stmt_item->execute([
            $order_id,
            $item['id'],
            $item['quantity'],
            $item['price']
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>