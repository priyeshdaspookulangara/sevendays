CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER,
    name TEXT NOT NULL,
    price REAL NOT NULL,
    description TEXT,
    image_url TEXT,
    is_featured INTEGER DEFAULT 0,
    is_star INTEGER DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT NOT NULL,
    address TEXT NOT NULL,
    total_amount REAL NOT NULL,
    status TEXT DEFAULT 'Pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER,
    product_id INTEGER,
    quantity INTEGER NOT NULL,
    price REAL NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL
);

-- Initial Categories
INSERT OR IGNORE INTO categories (name) VALUES ('Breakfast Companion'), ('Traditional Podi'), ('Herbal Drink'), ('Herbal Tea'), ('Caffeine Free'), ('Spice Blend'), ('Traditional Oil');

-- Initial Products
INSERT INTO products (category_id, name, price, description, image_url, is_star)
SELECT id, 'Millet Idli Podi', 150.00, 'Nutritious millet flours meet bold aromatic spices — protein-rich and wholesome.', 'https://sevendaysenterprises.com/images/millet-idli-powder-packet.png', 1
FROM categories WHERE name = 'Breakfast Companion';

INSERT INTO products (category_id, name, price, description, image_url)
SELECT id, 'Veppila Chammanthi Podi', 120.00, 'Curry leaves, roasted lentils and tangy tamarind — a taste of home in every spoonful.', 'https://sevendaysenterprises.com/images/veppila-chammmanthippodi.png'
FROM categories WHERE name = 'Traditional Podi';

INSERT INTO products (category_id, name, price, description, image_url)
SELECT id, 'Chukku Kaappi', 180.00, 'Traditional dry-ginger coffee — rich, soothing and deeply aromatic.', 'https://sevendaysenterprises.com/images/chukku-kaappi-packet.png'
FROM categories WHERE name = 'Herbal Drink';

INSERT INTO products (category_id, name, price, description, image_url, is_featured)
SELECT id, 'Ginger Tea', 140.00, 'Hand-picked ginger and warming spices — every sip a gentle, natural awakening.', 'https://sevendaysenterprises.com/images/ginger-tea-package.png', 1
FROM categories WHERE name = 'Herbal Tea';

INSERT INTO products (category_id, name, price, description, image_url)
SELECT id, 'Herbal Coffee', 200.00, 'Rich, roasty and full of antioxidants — the ritual without the jolt.', 'https://sevendaysenterprises.com/images/ragi-idli-powder-packet.png'
FROM categories WHERE name = 'Caffeine Free';

INSERT INTO products (category_id, name, price, description, image_url, is_featured)
SELECT id, 'Signature Masala', 90.00, 'Freshly ground spices balanced to perfection — elevate every curry and marinade.', 'https://sevendaysenterprises.com/images/signature-masala.png', 1
FROM categories WHERE name = 'Spice Blend';

INSERT INTO products (category_id, name, price, description, image_url)
SELECT id, 'Kerala Coconut Oil', 250.00, 'Cold-pressed from the finest Kerala coconuts — rich in natural goodness.', 'https://sevendaysenterprises.com/images/coconut-oil-kerala.png'
FROM categories WHERE name = 'Traditional Oil';

-- Default Admin: admin / admin123 (hashed using PASSWORD_DEFAULT)
INSERT OR IGNORE INTO admins (username, password) VALUES ('admin', '$2y$10$8W3Y6uO4vWdDqj.p8/E3U.W9jXWqZqF6m7XzG6j8r9/x.V.w5p5u.');
