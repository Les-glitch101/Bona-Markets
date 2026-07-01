CREATE DATABASE IF NOT EXISTS bona_markets;
USE bona_markets;

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL
);

INSERT INTO categories (id, name) VALUES
(1, 'Electronics'),
(2, 'Clothing'),
(3, 'Home & Garden'),
(4, 'Accessories'),
(5, 'Beauty & Wellness'),
(6, 'Handcrafts'),
(7, 'Food & Spices'),
(8, 'Art & Decor'),
(9, 'Jewellery');

-- Products Table
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  category_id INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image VARCHAR(255) NOT NULL,
  description TEXT,
  label VARCHAR(50),
  vendor VARCHAR(100),
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Products with linked images and vendors
INSERT INTO products (name, category_id, price, stock, image, description, label, vendor) VALUES
-- Electronics
('Laptop', 1, 12000.00, 10, 'laptop.jpg', 'High-performance laptop for work and gaming', 'BEST SELLER', 'Tech Vendor'),
('Smartphone', 1, 8000.00, 15, 'smartphone.jpg', 'Latest smartphone with great features', 'NEW', 'Tech Vendor'),
('Tablet', 1, 5000.00, 12, 'tablet.jpg', 'Portable tablet for work and play', NULL, 'Tech Vendor'),
('Gaming Console', 1, 7000.00, 8, 'console.jpg', 'Next-gen gaming console', NULL, 'GameWorld'),
('Headphones', 1, 1500.00, 20, 'headphones.jpg', 'Noise-cancelling headphones', 'NEW', 'SoundPro'),

-- Clothing
('Denim Jacket', 2, 899.00, 25, 'jacket.jpg', 'Stylish denim jacket', 'BEST SELLER', 'Fashion Hub'),
('Dress', 2, 1200.00, 18, 'dress.jpg', 'Elegant evening dress', NULL, 'Fashion Hub'),
('Shirt', 2, 450.00, 30, 'shirt.jpg', 'Casual cotton shirt', NULL, 'Fashion Hub'),
('Sneakers', 2, 950.00, 20, 'sneakers.jpg', 'Comfortable sneakers for everyday wear', NULL, 'Fashion Hub'),

-- Home & Garden
('Blanket', 3, 600.00, 15, 'blanket.jpg', 'Soft and cozy blanket', NULL, 'HomeStyle'),
('Lamp', 3, 750.00, 10, 'lamp.jpg', 'Modern decorative lamp', NULL, 'HomeStyle'),
('Vacuum Cleaner', 3, 2500.00, 5, 'vacuum.jpg', 'Powerful vacuum cleaner', NULL, 'HomeStyle'),
('Mirror', 3, 800.00, 12, 'mirror.jpg', 'Wall mirror with frame', NULL, 'HomeStyle'),

-- Accessories
('Handbag', 4, 950.00, 20, 'handbag.jpg', 'Leather handbag', NULL, 'Accessory World'),
('Wallet', 4, 500.00, 25, 'wallet.jpg', 'Classic leather wallet', NULL, 'Accessory World'),
('Sunglasses', 4, 700.00, 30, 'sunglasses.jpg', 'Stylish sunglasses', NULL, 'Accessory World'),
('Watch', 4, 1500.00, 10, 'watch.jpg', 'Elegant wrist watch', NULL, 'Accessory World'),

-- Beauty & Wellness
('Perfume', 5, 450.00, 50, 'perfume.jpg', 'Luxury fragrance', 'BEST SELLER', 'Beauty Co'),
('Lip Balm', 5, 120.00, 40, 'lipbalm.jpg', 'Moisturizing lip balm', NULL, 'Beauty Co'),
('Lotion', 5, 300.00, 35, 'lotion.jpg', 'Skin care lotion', NULL, 'Beauty Co'),
('Shampoo', 5, 250.00, 25, 'shampoo.jpg', 'Hair care shampoo', NULL, 'Beauty Co'),

-- Handcrafts
('Beads', 6, 300.00, 40, 'beads.jpg', 'Handcrafted decorative beads', NULL, 'Crafts Corner'),
('Basket', 6, 250.00, 20, 'basket.jpg', 'Handwoven basket', NULL, 'Crafts Corner'),
('Scarf', 6, 350.00, 15, 'scarf.jpg', 'Handmade wool scarf', NULL, 'Crafts Corner'),
('Cufflinks', 6, 400.00, 10, 'cufflinks.jpg', 'Handmade cufflinks', NULL, 'Crafts Corner'),

-- Food & Spices
('Coffee', 7, 200.00, 50, 'coffee.jpg', 'Premium roasted coffee beans', NULL, 'Foodies'),
('Spices Pack', 7, 150.00, 60, 'spices.jpg', 'Mixed spices pack', NULL, 'Foodies'),
('Honey', 7, 180.00, 25, 'honey.jpg', 'Organic honey jar', NULL, 'Foodies'),
('Tea', 7, 160.00, 30, 'tea.jpg', 'Herbal tea pack', NULL, 'Foodies'),

-- Art & Decor
('Painting', 8, 3000.00, 5, 'painting.jpg', 'Hand-painted artwork', NULL, 'ArtHouse'),
('Decor Vase', 8, 1200.00, 10, 'decorvase.jpg', 'Ceramic decorative vase', NULL, 'ArtHouse'),
('Frames', 8, 600.00, 15, 'frames.jpg', 'Photo frames set', NULL, 'ArtHouse'),
('Sculpture', 8, 3500.00, 3, 'sculpture.jpg', 'Hand-carved sculpture', NULL, 'ArtHouse'),

-- Jewellery
('Gold Necklace', 9, 5000.00, 8, 'goldnecklace.jpg', 'Elegant gold necklace', 'BEST SELLER', 'Jewels Inc'),
('Earrings', 9, 1200.00, 15, 'earrings.jpg', 'Silver earrings', NULL, 'Jewels Inc'),
('Ring', 9, 2500.00, 10, 'ring.jpg', 'Diamond ring', NULL, 'Jewels Inc'),
('Bracelet', 9, 1800.00, 12, 'bracelet.jpg', 'Gold bracelet', NULL, 'Jewels Inc');
