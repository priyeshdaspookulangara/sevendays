# Sevendays Enterprises E-commerce Platform

A dynamic e-commerce application built with PHP and MySQL, featuring a modern responsive design and an integrated admin panel.

## Features
- Dynamic product catalog with category filtering.
- Detailed product pages with related item suggestions.
- Shopping cart and favorites management (client-side persistence).
- Secure checkout process with server-side price validation.
- Admin dashboard for product CRUD and order tracking.
- Brand-consistent aesthetic using Bootstrap 5 and custom CSS.

## Setup Instructions

### Prerequisites
- PHP 8.3 or higher.
- MySQL 8.0 or higher.
- A web server (Apache, Nginx, or PHP's built-in server for development).

### Database Setup
1. Create a new MySQL database named `ecommerce`.
2. Import the database schema and initial data:
   ```bash
   mysql -u your_username -p ecommerce < db/schema.sql
   ```
   *(Replace `your_username` with your MySQL username).*

### Application Configuration
1. Open `includes/db_connect.php`.
2. Update the connection parameters to match your MySQL environment:
   ```php
   $host = 'localhost';
   $db   = 'ecommerce';
   $user = 'root';
   $pass = 'your_password';
   ```

### Running the Application
If you are using the PHP built-in server, run the following command from the project root:
```bash
php -S localhost:8000
```
Then visit `http://localhost:8000` in your browser.

## Admin Access
- **URL:** `http://localhost:8000/admin`
- **Default Username:** `admin`
- **Default Password:** `admin123`

## Project Structure
- `admin/`: Management dashboard (orders, products).
- `assets/`: Custom CSS and client-side JavaScript.
- `db/`: SQL schema files.
- `includes/`: Database connection and shared functions.
- `api/`: Backend endpoints for data retrieval.
- `index.php`: Homepage.
- `product.php`: Product details page.
- `cart.php`: Shopping cart and checkout.
- `favorites.php`: Wishlist page.
