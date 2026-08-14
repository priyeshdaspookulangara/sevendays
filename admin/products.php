<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$categories = get_categories($pdo);
$csrf_token = generate_csrf_token();

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: products.php');
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    $id = !empty($_POST['id']) && is_scalar($_POST['id']) ? (int)$_POST['id'] : null;
    $name = $_POST['name'] ?? '';
    $price = !empty($_POST['price']) && is_scalar($_POST['price']) ? (float)$_POST['price'] : 0.00;
    $category_id = !empty($_POST['category_id']) && is_scalar($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $description = $_POST['description'] ?? '';
    $image_url = $_POST['image_url'] ?? '';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_star = isset($_POST['is_star']) ? 1 : 0;

    if ($is_star) {
        // Only one star product allowed
        $pdo->query("UPDATE products SET is_star = 0");
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, category_id=?, description=?, image_url=?, is_featured=?, is_star=? WHERE id=?");
        $stmt->execute([$name, $price, $category_id, $description, $image_url, $is_featured, $is_star, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, category_id, description, image_url, is_featured, is_star) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $category_id, $description, $image_url, $is_featured, $is_star]);
    }
    header('Location: products.php');
    exit;
}

$products = get_all_products($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
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
        <a href="products.php" class="active"><i class="fa-solid fa-box me-2"></i> Products</a>
        <a href="orders.php"><i class="fa-solid fa-shopping-cart me-2"></i> Orders</a>
        <a href="logout.php" class="mt-5 text-danger"><i class="fa-solid fa-sign-out me-2"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Products</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" onclick="resetForm()">Add New Product</button>
        </div>

        <div class="stat-card">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Star</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo e($product['name']); ?></td>
                            <td><?php echo e($product['category_name']); ?></td>
                            <td>₹<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['is_star'] ? '⭐' : ''; ?></td>
                            <td><?php echo $product['is_featured'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-info" onclick='editProduct(<?php echo htmlspecialchars(json_encode($product), ENT_QUOTES, "UTF-8"); ?>)'>Edit</button>
                                    <form method="POST" onsubmit="return confirm('Are you sure?')">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="prod_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="prod_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" id="prod_cat" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo e($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" name="price" id="prod_price" step="0.01" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image URL</label>
                        <input type="text" name="image_url" id="prod_img" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="prod_desc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" id="prod_featured" class="form-check-input">
                            <label class="form-check-label">Featured</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_star" id="prod_star" class="form-check-input">
                            <label class="form-check-label">Star Product</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="save" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.getElementById('prod_id').value = '';
            document.getElementById('prod_name').value = '';
            document.getElementById('prod_price').value = '';
            document.getElementById('prod_img').value = '';
            document.getElementById('prod_desc').value = '';
            document.getElementById('prod_featured').checked = false;
            document.getElementById('prod_star').checked = false;
        }

        function editProduct(product) {
            document.getElementById('prod_id').value = product.id;
            document.getElementById('prod_name').value = product.name;
            document.getElementById('prod_cat').value = product.category_id;
            document.getElementById('prod_price').value = product.price;
            document.getElementById('prod_img').value = product.image_url;
            document.getElementById('prod_desc').value = product.description;
            document.getElementById('prod_featured').checked = product.is_featured == 1;
            document.getElementById('prod_star').checked = product.is_star == 1;

            new bootstrap.Modal(document.getElementById('productModal')).show();
        }
    </script>
</body>
</html>