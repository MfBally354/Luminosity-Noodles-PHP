-- Luminosity Noodles Database Schema
CREATE DATABASE IF NOT EXISTS luminosity_noodles CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE luminosity_noodles;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    is_available TINYINT(1) DEFAULT 1,
    has_spicy TINYINT(1) DEFAULT 1,
    has_topping TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE toppings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    extra_price DECIMAL(10,2) DEFAULT 0
);

CREATE TABLE promos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_percent INT NOT NULL,
    min_order DECIMAL(10,2) DEFAULT 0,
    expires_at DATE,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    promo_id INT DEFAULT NULL,
    status ENUM('pending','processing','cooking','delivering','done','cancelled') DEFAULT 'pending',
    subtotal DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    notes TEXT,
    payment_method ENUM('cash','transfer') DEFAULT 'cash',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (promo_id) REFERENCES promos(id) ON DELETE SET NULL
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    spicy_level ENUM('none','mild','medium','hot','extra_hot') DEFAULT 'none',
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(id)
);

CREATE TABLE order_item_toppings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_item_id INT NOT NULL,
    topping_id INT NOT NULL,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE,
    FOREIGN KEY (topping_id) REFERENCES toppings(id)
);

-- Seed data
INSERT INTO users (name, email, password, role) VALUES
('Admin Luminosity', 'admin@luminosity.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Default admin password: password

INSERT INTO categories (name, slug) VALUES
('Signature Noodles', 'signature'),
('Soup Noodles', 'soup'),
('Dry Noodles', 'dry'),
('Rice Bowls', 'rice'),
('Drinks', 'drinks');

INSERT INTO toppings (name, extra_price) VALUES
('Extra Chashu', 5000),
('Soft Boiled Egg', 3000),
('Extra Noodles', 4000),
('Corn', 2000),
('Bamboo Shoots', 2000),
('Nori', 2000),
('Extra Broth', 0);

INSERT INTO menus (category_id, name, description, price, image, has_spicy, has_topping) VALUES
(1, 'Cosmic Ramen', 'Signature black garlic broth with cosmic vibes, thick noodles, and chashu pork', 65000, 'cosmic-ramen.jpg', 1, 1),
(1, 'Nebula Mie Ayam', 'Tender chicken with premium seasoning and rich black sesame sauce', 45000, 'nebula-mie.jpg', 1, 1),
(2, 'Galaxy Pho', 'Vietnamese-inspired clear beef broth with stardust spice blend', 55000, 'galaxy-pho.jpg', 1, 1),
(2, 'Aurora Tonkotsu', 'Rich creamy pork bone broth inspired by northern lights', 70000, 'aurora-tonk.jpg', 1, 1),
(3, 'Black Hole Mie Goreng', 'Dry stir-fried noodles with dark squid ink and umami overload', 50000, 'blackhole-mie.jpg', 1, 1),
(4, 'Supernova Rice Bowl', 'Explosive flavors — teriyaki chicken over steamed rice with cosmic sauce', 48000, 'supernova-rice.jpg', 1, 0),
(5, 'Stardust Lemonade', 'Blue butterfly pea lemonade with shimmer effect', 18000, 'stardust-lemon.jpg', 0, 0),
(5, 'Dark Matter Coffee', 'Strong cold brew with coconut milk and dark caramel', 22000, 'dark-matter-coffee.jpg', 0, 0);

INSERT INTO promos (code, discount_percent, min_order, expires_at) VALUES
('LAUNCH25', 25, 50000, '2025-12-31'),
('COSMIC10', 10, 30000, '2025-12-31');
