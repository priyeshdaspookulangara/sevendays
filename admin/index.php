<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$product_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$order_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
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
        <a href="index.php" class="active"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
        <a href="products.php"><i class="fa-solid fa-box me-2"></i> Products</a>
        <a href="orders.php"><i class="fa-solid fa-shopping-cart me-2"></i> Orders</a>
        <a href="logout.php" class="mt-5 text-danger"><i class="fa-solid fa-sign-out me-2"></i> Logout</a>
    </div>

    <div class="main-content">
        <h2 class="mb-4">Welcome, <?php echo e($_SESSION['admin_username']); ?></h2>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <h5 class="text-muted">Total Products</h5>
                    <h3><?php echo $product_count; ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5 class="text-muted">Total Orders</h5>
                    <h3><?php echo $order_count; ?></h3>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <h4 class="mb-4">Recent Orders</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo e($order['first_name'] . ' ' . $order['last_name']); ?></td>
                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><span class="badge bg-warning"><?php echo e($order['status']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>