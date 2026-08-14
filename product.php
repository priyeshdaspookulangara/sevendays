<?php
require_once __DIR__ . '/includes/db_connect.php';

$id = isset($_GET['id']) && is_scalar($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}

$stmt_related = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 3");
$stmt_related->execute([$product['category_id'], $id]);
$related_products = $stmt_related->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo e($product['name']); ?> – Sevendays Enterprises</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Jost:wght@300;400;500;600&family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,700;1,9..144,400&display=swap" rel="stylesheet"/>

  <style>
    :root {
      --ink:       #1a1612;
      --charcoal:  #2e2b27;
      --stone:     #6b6560;
      --pebble:    #a8a09a;
      --linen:     #f5f0e8;
      --white:     #ffffff;
      --cream:     #faf7f2;
      --terra:     #c1622f;
      --terra-lt:  #e8896a;
      --sage:      #2C5E1A;
      --gold:      #C29208;
      --border:    rgba(26,22,18,0.1);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: 'Jost', sans-serif; font-weight: 300; background: var(--cream); color: var(--charcoal); overflow-x: hidden; }

    .navbar { background: var(--white); border-bottom: 1px solid var(--border); padding: .9rem 0; }
    .nav-link { font-family: 'Jost', sans-serif; font-weight: 500; font-size: .78rem; letter-spacing: .18em; text-transform: uppercase; color: var(--charcoal) !important; padding: .4rem .9rem !important; transition: color .25s; }
    .nav-link:hover { color: var(--terra) !important; }
    .btn-nav { font-family: 'Jost', sans-serif; font-weight: 500; font-size: .75rem; letter-spacing: .18em; text-transform: uppercase; background: var(--ink); color: var(--white) !important; padding: .55rem 1.6rem; border: none; text-decoration: none; }
    .nav-cart-btn { position: relative; cursor: pointer; margin-left: 1rem; }
    .cart-badge { position: absolute; top: -5px; right: -10px; background: var(--terra); color: var(--white); font-size: 0.65rem; padding: 2px 6px; border-radius: 50%; }

    .product-details { padding: 8rem 0 5rem; }
    .product-img-large { width: 100%; height: 600px; object-fit: cover; background: var(--white); }
    .tag-pill { font-family: 'Jost', sans-serif; font-weight: 500; font-size: .7rem; letter-spacing: .2em; text-transform: uppercase; color: var(--terra); background: rgba(193,98,47,.08); border: 1px solid rgba(193,98,47,.2); padding: .3rem 1rem; display: inline-block; margin-bottom: 1.25rem; }
    .display-serif { font-family: 'Fraunces', serif; font-weight: 700; font-size: 3rem; line-height: 1.15; color: var(--ink); }
    .price-tag { font-size: 2rem; color: var(--terra); font-weight: 500; margin: 1.5rem 0; }
    .body-text { font-family: 'Jost', sans-serif; font-weight: 300; font-size: 1.1rem; line-height: 1.8; color: var(--stone); }

    .btn-terra { font-family: 'Jost', sans-serif; font-weight: 500; font-size: .78rem; letter-spacing: .18em; text-transform: uppercase; background: var(--terra); color: var(--white); padding: 1.1rem 3rem; border: none; text-decoration: none; display: inline-block; transition: background .3s, transform .3s; }
    .btn-terra:hover { background: var(--charcoal); transform: translateY(-3px); }
    .btn-outline-dark { font-family: 'Jost', sans-serif; font-weight: 500; font-size: .78rem; letter-spacing: .18em; text-transform: uppercase; background: transparent; color: var(--ink); padding: 1.1rem 2rem; border: 1.5px solid var(--ink); text-decoration: none; display: inline-block; }

    .qty-btn { width: 32px; height: 32px; border: 1px solid var(--pebble); background: none; cursor: pointer; display: flex; align-items: center; justify-content: center; }

    .fav-btn { color: var(--pebble); font-size: 1.5rem; transition: color 0.3s; cursor: pointer; }
    .fav-btn.active { color: #e74c3c; }

    .related-section { padding: 5rem 0; background: var(--white); }
    .prod-card { background: var(--white); transition: 0.3s; border: 1px solid var(--border); }
    .prod-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .prod-card-img { height: 250px; width: 100%; object-fit: cover; }

    footer { background: var(--ink); color: rgba(255,255,255,.5); padding: 4rem 0 2rem; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand p-0" href="index.php">
      <span style="font-family:'Fraunces',serif;font-weight:700;font-size:1.3rem;color:var(--ink);">Sevendays</span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <i class="fa-solid fa-bars" style="color:var(--ink);font-size:1.1rem;"></i>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav gap-1 mx-auto">
        <li class="nav-item"><a class="nav-link" href="index.php#products">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="favorites.php">Favorites</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#contact">Contact</a></li>
      </ul>
    </div>
    <div class="d-flex align-items-center">
      <a href="favorites.php" class="me-3 position-relative">
        <i class="fa-regular fa-heart" id="navFavIcon" style="color:var(--ink);font-size:1.2rem;"></i>
        <span class="cart-badge d-none" id="favCount">0</span>
      </a>
      <a href="cart.php" class="nav-cart-btn me-3">
        <i class="fa-solid fa-cart-shopping" style="color:var(--ink);font-size:1.2rem;"></i>
        <span class="cart-badge" id="cartCount">0</span>
      </a>
      <a href="index.php#order" class="btn-nav d-none d-lg-inline-block">Order Now</a>
    </div>
  </div>
</nav>

<section class="product-details">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img-large">
            </div>
            <div class="col-lg-6">
                <span class="tag-pill"><?php echo e($product['category_name']); ?></span>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h1 class="display-serif"><?php echo e($product['name']); ?></h1>
                    <i class="fa-regular fa-heart fav-btn" id="favBtn" data-id="<?php echo $product['id']; ?>"></i>
                </div>
                <div class="price-tag">₹<?php echo number_format($product['price'], 2); ?></div>
                <p class="body-text mb-5"><?php echo e($product['description']); ?></p>

                <div class="d-flex gap-3 align-items-center mb-5"
                     data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>"
                     data-price="<?php echo $product['price']; ?>" data-img="<?php echo htmlspecialchars($product['image_url']); ?>">
                    <div class="d-flex align-items-center border p-2">
                        <button class="qty-btn" id="qtyDown">-</button>
                        <span class="mx-4 fw-bold" id="qtyNum">1</span>
                        <button class="qty-btn" id="qtyUp">+</button>
                    </div>
                    <button class="btn-terra px-5 add-to-cart-page">Add to Basket</button>
                </div>

                <div class="pt-4 border-top">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <i class="fa-solid fa-leaf text-success mt-1"></i>
                                <div>
                                    <p class="fw-bold mb-0">100% Organic</p>
                                    <p class="small text-muted">Directly from Kerala farms</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <i class="fa-solid fa-truck text-warning mt-1"></i>
                                <div>
                                    <p class="fw-bold mb-0">Fast Shipping</p>
                                    <p class="small text-muted">Freshly packed to your door</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($related_products)): ?>
<section class="related-section">
    <div class="container">
        <h2 class="display-serif mb-5" style="font-size: 2rem;">Related Products</h2>
        <div class="row g-4">
            <?php foreach ($related_products as $rp): ?>
            <div class="col-md-4">
                <div class="prod-card" data-id="<?php echo $rp['id']; ?>" data-name="<?php echo htmlspecialchars($rp['name']); ?>" data-price="<?php echo $rp['price']; ?>" data-img="<?php echo htmlspecialchars($rp['image_url']); ?>">
                    <a href="product.php?id=<?php echo $rp['id']; ?>">
                        <img src="<?php echo htmlspecialchars($rp['image_url']); ?>" class="prod-card-img" alt="<?php echo htmlspecialchars($rp['name']); ?>">
                    </a>
                    <div class="p-4">
                        <h4 class="mb-2" style="font-family: 'Fraunces', serif;"><?php echo e($rp['name']); ?></h4>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">₹<?php echo number_format($rp['price'], 2); ?></span>
                            <button class="btn btn-sm btn-outline-dark add-to-cart">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<footer>
  <div class="container text-center">
    <p>© 2025 Sevendays Enterprises. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    let cart = JSON.parse(localStorage.getItem('sevendays_cart')) || [];
    let favs = JSON.parse(localStorage.getItem('sevendays_favs')) || [];

    const cartCountEl = document.getElementById('cartCount');
    const favCountEl = document.getElementById('favCount');
    const favBtn = document.getElementById('favBtn');
    const navFavIcon = document.getElementById('navFavIcon');

    function updateCounts() {
        let count = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCountEl.textContent = count;

        favCountEl.textContent = favs.length;
        favCountEl.classList.toggle('d-none', favs.length === 0);

        if (favs.length > 0) {
            navFavIcon?.classList.remove('fa-regular');
            navFavIcon?.classList.add('fa-solid');
        } else {
            navFavIcon?.classList.add('fa-regular');
            navFavIcon?.classList.remove('fa-solid');
        }

        if (favBtn) {
            const id = favBtn.dataset.id;
            favBtn.classList.toggle('active', favs.includes(id));
            favBtn.classList.toggle('fa-solid', favs.includes(id));
            favBtn.classList.toggle('fa-regular', !favs.includes(id));
        }
    }

    // Qty Logic
    let qty = 1;
    const qtyNum = document.getElementById('qtyNum');
    document.getElementById('qtyDown')?.addEventListener('click', () => { if(qty > 1) { qty--; qtyNum.textContent = qty; } });
    document.getElementById('qtyUp')?.addEventListener('click', () => { qty++; qtyNum.textContent = qty; });

    // Add to cart
    document.querySelector('.add-to-cart-page')?.addEventListener('click', function() {
        const container = this.parentElement;
        const product = {
            id: container.dataset.id,
            name: container.dataset.name,
            price: parseFloat(container.dataset.price),
            img: container.dataset.img,
            quantity: qty
        };

        const existing = cart.find(item => item.id === product.id);
        if (existing) {
            existing.quantity += qty;
        } else {
            cart.push(product);
        }
        localStorage.setItem('sevendays_cart', JSON.stringify(cart));
        updateCounts();
        alert("Added to basket!");
    });

    // Favorites
    favBtn?.addEventListener('click', function() {
        const id = this.dataset.id;
        const index = favs.indexOf(id);
        if (index > -1) {
            favs.splice(index, 1);
        } else {
            favs.push(id);
        }
        localStorage.setItem('sevendays_favs', JSON.stringify(favs));
        updateCounts();
    });

    // Related Add to Cart
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.prod-card');
            const product = {
                id: card.dataset.id,
                name: card.dataset.name,
                price: parseFloat(card.dataset.price),
                img: card.dataset.img,
                quantity: 1
            };
            const existing = cart.find(item => item.id === product.id);
            if (existing) existing.quantity++;
            else cart.push(product);
            localStorage.setItem('sevendays_cart', JSON.stringify(cart));
            updateCounts();
            alert("Added to basket!");
        });
    });

    updateCounts();
})();
</script>
</body>
</html>