<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$csrf_token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    $id = $_POST['id'];
    $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$id]);
    header('Location: orders.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
    <style>
        :root { --terra: #c1622f; --ink: #1a1612; }
        body { background: #faf7f2; font-family: 'Jost', sans-serif; }
        .sidebar { width: 250px; height: 100vh; background: var(--ink); color: #fff; position: fixed; padding: 2rem 1rem; }
        .sidebar a { color: rgba(255,255,255,0.7); text-decoration: none; display: block; padding: 0.75rem 1rem; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: rgba(255,255,255,0.1); }
        .main-content { margin-left: 250px; padding: 3rem; }
        .stat-card { background: #fff; padding: 2rem; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="mb-5 text-center" style="font-family: 'Fraunces', serif;">Sevendays</h4>
        <a href="index.php"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
        <a href="products.php"><i class="fa-solid fa-box me-2"></i> Products</a>
        <a href="orders.php" class="active"><i class="fa-solid fa-shopping-cart me-2"></i> Orders</a>
        <a href="logout.php" class="mt-5 text-danger"><i class="fa-solid fa-sign-out me-2"></i> Logout</a>
    </div>

    <div class="main-content">
        <h2>Orders</h2>

        <div class="stat-card">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo e($order['first_name'] . ' ' . $order['last_name']); ?></td>
                            <td><?php echo e($order['email']); ?></td>
                            <td><?php echo e($order['phone']); ?></td>
                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Are you sure?')">
                                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>