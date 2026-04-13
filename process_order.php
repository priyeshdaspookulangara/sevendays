<?php
header('Content-Type: application/json');
require_once __DIR__ . '/includes/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

try {
    $pdo->beginTransaction();

    $total = 0;
    foreach ($data['cart'] as $item) {
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