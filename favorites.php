<?php
require_once __DIR__ . '/includes/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Favorites – Sevendays Enterprises</title>
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
      --border:    rgba(26,22,18,0.1);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Jost', sans-serif; font-weight: 300; background: var(--cream); color: var(--charcoal); min-height: 100vh; display: flex; flex-direction: column; }

    .navbar { background: var(--white); border-bottom: 1px solid var(--border); padding: .9rem 0; }
    .nav-link { font-family: 'Jost', sans-serif; font-weight: 500; font-size: .78rem; letter-spacing: .18em; text-transform: uppercase; color: var(--charcoal) !important; padding: .4rem .9rem !important; transition: color 0.25s; }
    .nav-link:hover { color: var(--terra) !important; }
    .btn-nav { font-family: 'Jost', sans-serif; font-weight: 500; font-size: .75rem; letter-spacing: .18em; text-transform: uppercase; background: var(--ink); color: var(--white) !important; padding: .55rem 1.6rem; border: none; text-decoration: none; }
    .cart-badge { position: absolute; top: -5px; right: -10px; background: var(--terra); color: var(--white); font-size: 0.65rem; padding: 2px 6px; border-radius: 50%; }

    .favorites-header { padding: 8rem 0 3rem; text-align: center; }
    .display-serif { font-family: 'Fraunces', serif; font-weight: 700; font-size: 3rem; color: var(--ink); }

    .fav-grid { padding-bottom: 5rem; }
    .prod-card { background: var(--white); transition: 0.3s; border: 1px solid var(--border); height: 100%; display: flex; flex-direction: column; }
    .prod-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .prod-card-img { height: 250px; width: 100%; object-fit: cover; }
    .btn-terra { font-family: 'Jost', sans-serif; font-weight: 500; font-size: .75rem; letter-spacing: .1em; text-transform: uppercase; background: var(--terra); color: var(--white); padding: 0.7rem 1.2rem; border: none; text-decoration: none; transition: background 0.3s, transform 0.3s; width: 100%; text-align: center; }
    .btn-terra:hover { background: var(--charcoal); transform: translateY(-3px); }

    .empty-state { padding: 5rem 0; text-align: center; color: var(--pebble); }
    .empty-state i { font-size: 4rem; margin-bottom: 1.5rem; display: block; }

    footer { background: var(--ink); color: rgba(255,255,255,.5); padding: 4rem 0 2rem; margin-top: auto; }
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
        <i class="fa-solid fa-heart" id="navFavIcon" style="color:var(--terra);font-size:1.2rem;"></i>
        <span class="cart-badge d-none" id="favCount">0</span>
      </a>
      <a href="cart.php" class="position-relative">
        <i class="fa-solid fa-cart-shopping" style="color:var(--ink);font-size:1.2rem;"></i>
        <span class="cart-badge" id="cartCount">0</span>
      </a>
      <a href="index.php#order" class="btn-nav d-none d-lg-inline-block">Order Now</a>
    </div>
  </div>
</nav>

<header class="favorites-header">
    <div class="container">
        <h1 class="display-serif">Your Favorites</h1>
        <p class="body-text">Items you've saved for later.</p>
    </div>
</header>

<section class="fav-grid">
    <div class="container">
        <div id="favItemsContainer" class="row g-4">
            <!-- Dynamically populated -->
        </div>
        <div id="emptyState" class="empty-state d-none">
            <i class="fa-regular fa-heart"></i>
            <h3>No favorites yet</h3>
            <p>Start exploring our products and save your favorites!</p>
            <a href="index.php#products" class="btn-terra mt-3" style="width: auto;">Browse Products</a>
        </div>
    </div>
</section>

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
    const navFavIcon = document.getElementById('navFavIcon');
    const container = document.getElementById('favItemsContainer');
    const emptyState = document.getElementById('emptyState');

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
    }

    async function renderFavs() {
        if (favs.length === 0) {
            emptyState.classList.remove('d-none');
            container.innerHTML = '';
            return;
        }

        emptyState.classList.add('d-none');
        container.innerHTML = '<div class="text-center w-100"><div class="spinner-border text-primary" role="status"></div></div>';

        try {
            // Fetch product details for all favorites
            const response = await fetch('api/get_products.php?ids=' + favs.join(','));
            const products = await response.json();

            container.innerHTML = '';
            products.forEach(p => {
                const col = document.createElement('div');
                col.className = 'col-md-4 col-lg-3';
                col.innerHTML = `
                    <div class="prod-card" data-id="${p.id}" data-name="${p.name}" data-price="${p.price}" data-img="${p.image_url}">
                        <div class="position-relative">
                            <a href="product.php?id=${p.id}">
                                <img src="${p.image_url}" class="prod-card-img" alt="${p.name}">
                            </a>
                            <button class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 remove-fav" style="border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="p-4 flex-grow-1 d-flex flex-column">
                            <h4 class="mb-2" style="font-family: 'Fraunces', serif; font-size: 1.1rem;">${p.name}</h4>
                            <div class="mt-auto">
                                <div class="fw-bold mb-3">₹${parseFloat(p.price).toFixed(2)}</div>
                                <button class="btn-terra w-100 add-to-cart">Add to Basket</button>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(col);
            });

            // Re-attach event listeners
            document.querySelectorAll('.remove-fav').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.closest('.prod-card').dataset.id;
                    favs = favs.filter(f => f !== id);
                    localStorage.setItem('sevendays_favs', JSON.stringify(favs));
                    updateCounts();
                    renderFavs();
                });
            });

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

        } catch (err) {
            console.error(err);
            container.innerHTML = '<p class="text-center text-danger">Error loading favorites.</p>';
        }
    }

    updateCounts();
    renderFavs();
})();
</script>
</body>
</html>