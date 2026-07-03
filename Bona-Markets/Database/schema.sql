-- ================================================================
-- BONA MARKETS � Complete Database Schema
-- ================================================================
-- 
-- This schema includes all tables needed for the Bona Markets
-- multi-vendor marketplace.
--
-- Tables included:
--   1. users           � All platform users (buyers, vendors, admins)
--   2. vendor_profiles � Vendor applications and profiles
--   3. categories      � Product categories
--   4. products        � Vendor product listings
--   5. cart            � Shopping cart items
--   6. orders          � Customer orders
--   7. order_items     � Individual items within orders
--   8. reviews         � Product reviews (ready for future use)
--
-- ================================================================

-- ================================================================
-- 1. USERS TABLE
-- Stores all user accounts on the platform
-- ================================================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    passwordHash VARCHAR(255) NOT NULL,
    role ENUM('buyer', 'vendor', 'admin') DEFAULT 'buyer',
    fullname VARCHAR(255) NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
);
-- 
-- Columns:
--   id          � Unique user ID (auto-increment)
--   email       � Login email (must be unique)
--   passwordHash � Hashed password (plain text in demo)
--   role        � User role: buyer, vendor, or admin
--   fullname    � User's full name
--   createdAt   � Account creation date
--
-- Indexes: email (UNIQUE)


-- ================================================================
-- 2. VENDOR PROFILES TABLE
-- Stores vendor applications and profile information
-- ================================================================

CREATE TABLE vendor_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    logo_url VARCHAR(500) NULL,
    description TEXT NULL,
    bank_details TEXT NULL,
    approved BOOLEAN DEFAULT 0,
    applied_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    phone VARCHAR(20) NULL,
    city VARCHAR(100) NULL,
    country VARCHAR(100) NULL,
    owner_name VARCHAR(255) NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- 
-- Columns:
--   id            � Unique profile ID
--   user_id       � References users.id (must be unique)
--   business_name � Store/business name
--   logo_url      � Cloudinary URL for store logo
--   description   � Business description
--   bank_details  � Banking information for payouts
--   approved      � 0 = pending, 1 = approved
--   applied_at    � Application submission date
--   phone         � Contact phone number
--   city          � Business city/town
--   country       � Business country
--   owner_name    � Full name of business owner
--
-- Relationships: user_id ? users.id (CASCADE DELETE)


-- ================================================================
-- 3. CATEGORIES TABLE
-- Product categories for organizing listings
-- ================================================================

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);
-- 
-- Columns:
--   id   � Unique category ID
--   name � Category name (must be unique)
--


-- ================================================================
-- 4. PRODUCTS TABLE
-- Vendor product listings
-- ================================================================

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    category_id INT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(500) NULL,
    stock INT DEFAULT 0,
    status ENUM('active', 'draft', 'archived') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
-- 
-- Columns:
--   id          � Unique product ID
--   vendor_id   � References users.id (the vendor selling this)
--   category_id � References categories.id
--   name        � Product name
--   description � Product description
--   price       � Price in ZAR (decimal)
--   image_url   � Cloudinary URL for product image
--   stock       � Available quantity
--   status      � active, draft, or archived
--   created_at  � Listing date
--
-- Relationships: 
--   vendor_id ? users.id
--   category_id ? categories.id


-- ================================================================
-- 5. CART TABLE
-- Shopping cart items (per user)
-- ================================================================

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
-- 
-- Columns:
--   id         � Unique cart item ID
--   user_id    � References users.id (the buyer)
--   product_id � References products.id
--   quantity   � Quantity in cart
--   added_at   � When item was added
--
-- Relationships: user_id ? users.id, product_id ? products.id


-- ================================================================
-- 6. ORDERS TABLE
-- Customer orders (placed after checkout)
-- ================================================================

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'shipped', 'delivered') DEFAULT 'pending',
    address VARCHAR(500) NULL,
    payment_id VARCHAR(255) NULL,
    stripe_session_id VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
-- 
-- Columns:
--   id               � Unique order ID
--   user_id          � References users.id (the buyer)
--   total            � Total order amount
--   status           � pending, paid, shipped, delivered
--   stripe_session_id � Stripe checkout session ID (for payment tracking)
--   created_at       � Order date
--
-- Relationships: user_id ? users.id


-- ================================================================
-- 7. ORDER ITEMS TABLE
-- Individual items within an order
-- ================================================================

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
-- 
-- Columns:
--   id         � Unique order item ID
--   order_id   � References orders.id
--   product_id � References products.id
--   quantity   � Quantity purchased
--   price      � Price at time of purchase (snapshot)
--
-- Relationships: order_id ? orders.id, product_id ? products.id


-- ================================================================
-- 8. REVIEWS TABLE (Ready for future implementation)
-- Product reviews from buyers
-- ================================================================

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
-- 
-- Columns:
--   id         � Unique review ID
--   product_id � References products.id
--   user_id    � References users.id (the reviewer)
--   rating     � Rating between 1 and 5 stars
--   comment    � Review text
--   created_at � Review date
--
-- Relationships: product_id ? products.id, user_id ? users.id


-- ================================================================
-- SEED DATA � Demo users, categories, products, orders
-- ================================================================

-- ------------------------------------------------------------------
-- DEMO USERS
-- ------------------------------------------------------------------
-- Passwords stored as plain text for demo purposes.
-- In production, use password_hash() to store hashed passwords.
-- Demo credentials:
--   buyer@bonamarkets.com  / demo123
--   vendor@bonamarkets.com  / demo123
--   admin@bonamarkets.com   / demo123
-- ------------------------------------------------------------------

INSERT INTO users (email, passwordHash, role, fullname) VALUES
('demo@bonamarkets.com', 'demo123', 'buyer', 'Demo Buyer'),
('vendor@bonamarkets.com', 'demo123', 'vendor', 'Vendor User'),
('admin@bonamarkets.com', 'demo123', 'admin', 'Admin User');


-- ------------------------------------------------------------------
-- DEMO CATEGORIES
-- ------------------------------------------------------------------

INSERT INTO categories (name) VALUES
('Electronics'),
('Clothing'),
('Home & Garden'),
('Accessories'),
('Beauty & Wellness'),
('Handcrafts'),
('Food & Spices'),
('Art & Decor'),
('Jewellery');


-- ------------------------------------------------------------------
-- DEMO VENDOR PROFILE
-- ------------------------------------------------------------------
-- Vendor profile for vendor@bonamarkets.com (user_id = 2)
-- approved = 1 means the vendor is active and can sell
-- ------------------------------------------------------------------

INSERT INTO vendor_profiles (user_id, business_name, description, bank_details, approved, phone, city, country, owner_name) VALUES
(
    2,
    'Amara''s Artisan Market',
    'Handcrafted African goods made with love and tradition. Every piece tells a story.',
    'FNB Account: 1234567890',
    1,
    '+27 82 456 7890',
    'Johannesburg',
    'South Africa',
    'Amara Osei'
);


-- ------------------------------------------------------------------
-- DEMO PRODUCTS
-- ------------------------------------------------------------------
-- Products listed by vendor_id = 2 (vendor@bonamarkets.com)
-- Prices are in ZAR (South African Rand)
-- ------------------------------------------------------------------

INSERT INTO products (vendor_id, category_id, name, description, price, stock, status) VALUES
(2, 1, 'Wireless Headphones', 'High-quality Bluetooth headphones with noise cancellation. Perfect for work and travel.', 799.99, 14, 'active'),
(2, 2, 'Denim Jacket', 'Classic denim jacket, perfect for any season. Available in multiple sizes.', 899.99, 6, 'active'),
(2, 3, 'Smart LED Lamp', 'Color-changing smart lamp with app control. Set the mood with millions of colors.', 349.99, 20, 'active'),
(2, 4, 'Smart Watch Pro', 'Feature-packed smartwatch with health tracking, GPS, and heart rate monitor.', 1999.99, 2, 'active'),
(2, 7, 'Rooibos Tea Blend', 'Premium rooibos tea with baobab. Authentic South African flavour.', 120.00, 30, 'active'),
(2, 6, 'Hand-Carved Soapstone Bowl', 'Authentic hand-carved soapstone bowl from Zimbabwe. Each piece is unique.', 850.00, 0, 'active'),
(2, 5, 'Shea Butter Body Cream', 'Natural shea butter moisturizer with essential oils. Perfect for dry skin.', 195.00, 50, 'active'),
(2, 9, 'Maasai Beaded Bracelet', 'Hand-beaded bracelet inspired by Maasai tradition. Vibrant and colourful.', 340.00, 12, 'active'),
(2, 1, 'Gaming Keyboard', 'Mechanical RGB gaming keyboard with customisable lighting.', 1299.99, 8, 'active'),
(2, 8, 'Ankara Print Cushion Cover', 'Vibrant Ankara print cushion cover. Add a touch of Africa to your home.', 245.00, 20, 'draft');


-- ------------------------------------------------------------------
-- DEMO ORDERS
-- ------------------------------------------------------------------
-- Orders placed by buyer_id = 1 (demo@bonamarkets.com)
-- Status: pending, paid, shipped, delivered
-- ------------------------------------------------------------------

INSERT INTO orders (user_id, total, status, created_at) VALUES
(1, 320.00, 'pending', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 860.00, 'shipped', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1, 1240.00, 'delivered', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 360.00, 'delivered', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(1, 490.00, 'pending', DATE_SUB(NOW(), INTERVAL 1 DAY));


-- ------------------------------------------------------------------
-- DEMO ORDER ITEMS
-- ------------------------------------------------------------------
-- Each order contains one or more items
-- ------------------------------------------------------------------

INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
-- Order 1: Kente Cloth Tote Bag (product_id = 1)
(1, 1, 1, 320.00),
-- Order 2: Beaded Ndebele Necklace (product_id = 2)
(2, 2, 1, 580.00),
-- Order 3: 2x Moroccan Leather Pouch (product_id = 3)
(3, 3, 2, 460.00),
-- Order 4: 3x Rooibos Tea Blend (product_id = 5)
(4, 5, 3, 120.00),
-- Order 5: 2x Ankara Print Cushion Cover (product_id = 10)
(5, 10, 2, 245.00);


-- ------------------------------------------------------------------
-- DEMO CART ITEMS (Optional)
-- ------------------------------------------------------------------
-- Cart items for demo buyer (user_id = 1)
-- ------------------------------------------------------------------

INSERT INTO cart (user_id, product_id, quantity) VALUES
(1, 3, 1),
(1, 5, 2);


-- ================================================================
-- VERIFICATION QUERIES (Run these to check your data)
-- ================================================================

-- Check all users
SELECT id, email, role, fullname, createdAt FROM users;

-- Check vendor profile
SELECT u.email, v.business_name, v.approved, v.city, v.country 
FROM vendor_profiles v 
JOIN users u ON v.user_id = u.id;

-- Check categories
SELECT * FROM categories ORDER BY name;

-- Check products with category names
SELECT p.id, p.name, c.name as category, p.price, p.stock, p.status 
FROM products p 
LEFT JOIN categories c ON p.category_id = c.id;

-- Check orders with customer names
SELECT o.id, u.fullname as customer, o.total, o.status, o.created_at 
FROM orders o 
JOIN users u ON o.user_id = u.id 
ORDER BY o.created_at DESC;

-- Check order items
SELECT oi.order_id, p.name as product, oi.quantity, oi.price 
FROM order_items oi 
JOIN products p ON oi.product_id = p.id;

-- ================================================================
-- END OF SCHEMA
-- ================================================================

SELECT '? Database setup complete!' AS Message;