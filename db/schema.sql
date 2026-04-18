-- MySQL Schema for Sevendays Enterprises

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    is_star TINYINT(1) DEFAULT 0,
    INDEX (category_id),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    INDEX (order_id),
    INDEX (product_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Initial Categories
INSERT IGNORE INTO categories (name) VALUES
('Breakfast Companion'),
('Traditional Podi'),
('Herbal Drink'),
('Herbal Tea'),
('Caffeine Free'),
('Spice Blend'),
('Traditional Oil');

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

-- Default Admin: admin / admin123
INSERT IGNORE INTO admins (username, password) VALUES ('admin', '$2y$10$8W3Y6uO4vWdDqj.p8/E3U.W9jXWqZqF6m7XzG6j8r9/x.V.w5p5u.');
