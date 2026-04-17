<?php
require_once __DIR__ . '/includes/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Basket – Sevendays Enterprises</title>
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

    .cart-page { padding: 8rem 0 5rem; }
    .display-serif { font-family: 'Fraunces', serif; font-weight: 700; font-size: 3rem; color: var(--ink); }

    .cart-table { background: #fff; padding: 2rem; border: 1px solid var(--border); }
    .cart-item-img { width: 80px; height: 80px; object-fit: cover; background: var(--linen); }
    .qty-input-group { display: flex; align-items: center; border: 1px solid var(--border); width: fit-content; }
    .qty-btn { width: 32px; height: 32px; border: none; background: none; cursor: pointer; display: flex; align-items: center; justify-content: center; }

    .order-summary { background: var(--white); padding: 2rem; border: 1px solid var(--border); position: sticky; top: 100px; }
    .btn-terra { font-family: 'Jost', sans-serif; font-weight: 500; font-size: .78rem; letter-spacing: .18em; text-transform: uppercase; background: var(--terra); color: var(--white); padding: 1.1rem 2rem; border: none; text-decoration: none; width: 100%; text-align: center; display: inline-block; transition: background 0.3s, transform 0.3s; }
    .btn-terra:hover { background: var(--charcoal); transform: translateY(-3px); }

    .checkout-form label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 500; margin-bottom: 0.5rem; display: block; }
    .checkout-form input, .checkout-form textarea { border-radius: 0; border: 1px solid var(--border); padding: 0.75rem; font-family: 'Jost', sans-serif; margin-bottom: 1rem; }
    .checkout-form input:focus, .checkout-form textarea:focus { border-color: var(--terra); box-shadow: none; outline: none; }

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
        <i class="fa-regular fa-heart" id="navFavIcon" style="color:var(--ink);font-size:1.2rem;"></i>
        <span class="cart-badge d-none" id="favCount">0</span>
      </a>
      <a href="cart.php" class="position-relative">
        <i class="fa-solid fa-cart-shopping" style="color:var(--terra);font-size:1.2rem;"></i>
        <span class="cart-badge" id="cartCount">0</span>
      </a>
      <a href="index.php#order" class="btn-nav d-none d-lg-inline-block">Order Now</a>
    </div>
  </div>
</nav>

<section class="cart-page">
    <div class="container">
        <h1 class="display-serif mb-5">Your Basket</h1>

        <div id="cartContent">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="cart-table mb-4">
                        <div id="cartItemsList">
                            <!-- Items go here -->
                        </div>
                    </div>
                    <a href="index.php#products" class="btn btn-link p-0 text-dark text-decoration-none small"><i class="fa-solid fa-arrow-left me-2"></i> Continue Shopping</a>
                </div>
                <div class="col-lg-4">
                    <div class="order-summary">
                        <h3 class="mb-4" style="font-family: 'Fraunces', serif;">Order Summary</h3>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="subtotal">₹ 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span>Shipping</span>
                            <span class="text-success">Calculated at checkout</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold mb-4" style="font-size: 1.2rem;">
                            <span>Total</span>
                            <span id="total">₹ 0.00</span>
                        </div>

                        <div class="checkout-form mt-5">
                            <h4 class="mb-4" style="font-family: 'Fraunces', serif;">Shipping Details</h4>
                            <form id="cartCheckoutForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>First Name</label>
                                        <input type="text" id="firstName" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Last Name</label>
                                        <input type="text" id="lastName" class="form-control" required>
                                    </div>
                                </div>
                                <label>Email</label>
                                <input type="email" id="email" class="form-control" required>
                                <label>Phone</label>
                                <input type="tel" id="phone" class="form-control" required placeholder="+91">
                                <label>Address</label>
                                <textarea id="address" class="form-control" rows="3" required></textarea>
                                <button type="submit" class="btn-terra mt-3">Place Order</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="emptyCart" class="text-center py-5 d-none">
            <i class="fa-solid fa-basket-shopping mb-4" style="font-size: 4rem; color: var(--pebble);"></i>
            <h3>Your basket is empty</h3>
            <p class="body-text mb-4">Looks like you haven't added anything to your basket yet.</p>
            <a href="index.php#products" class="btn-terra" style="width: auto;">Browse Products</a>
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
    const itemsList = document.getElementById('cartItemsList');
    const emptyCart = document.getElementById('emptyCart');
    const cartContent = document.getElementById('cartContent');
    const subtotalEl = document.getElementById('subtotal');
    const totalEl = document.getElementById('total');

    function updateUI() {
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

        if (cart.length === 0) {
            cartContent.classList.add('d-none');
            emptyCart.classList.remove('d-none');
            return;
        }

        cartContent.classList.remove('d-none');
        emptyCart.classList.add('d-none');

        itemsList.innerHTML = '';
        let grandTotal = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            grandTotal += itemTotal;

            const div = document.createElement('div');
            div.className = 'd-flex gap-4 mb-4 pb-4 border-bottom align-items-center';
            div.innerHTML = `
                <img src="${item.img}" class="cart-item-img" alt="${item.name}">
                <div class="flex-grow-1">
                    <h5 class="mb-1" style="font-family: 'Fraunces', serif;">${item.name}</h5>
                    <div class="text-muted small">₹${item.price.toFixed(2)} / unit</div>
                </div>
                <div class="qty-input-group">
                    <button class="qty-btn" onclick="changeQty(${index}, -1)">-</button>
                    <span class="px-3 small fw-bold">${item.quantity}</span>
                    <button class="qty-btn" onclick="changeQty(${index}, 1)">+</button>
                </div>
                <div class="text-end" style="min-width: 100px;">
                    <div class="fw-bold">₹${itemTotal.toFixed(2)}</div>
                    <button class="btn btn-link p-0 text-danger small mt-1" onclick="removeItem(${index})"><i class="fa-solid fa-trash-can"></i></button>
                </div>
            `;
            itemsList.appendChild(div);
        });

        subtotalEl.textContent = `₹ ${grandTotal.toFixed(2)}`;
        totalEl.textContent = `₹ ${grandTotal.toFixed(2)}`;
    }

    window.changeQty = function(index, delta) {
        cart[index].quantity += delta;
        if (cart[index].quantity < 1) cart[index].quantity = 1;
        localStorage.setItem('sevendays_cart', JSON.stringify(cart));
        updateUI();
    };

    window.removeItem = function(index) {
        cart.splice(index, 1);
        localStorage.setItem('sevendays_cart', JSON.stringify(cart));
        updateUI();
    };

    document.getElementById('cartCheckoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = {
            first_name: document.getElementById('firstName').value,
            last_name: document.getElementById('lastName').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value,
            cart: cart
        };

        fetch('process_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Thank you! Your order has been placed successfully.");
                localStorage.removeItem('sevendays_cart');
                cart = [];
                updateUI();
            } else {
                alert("Error: " + data.message);
            }
        });
    });

    updateUI();
})();
</script>
</body>
</html>