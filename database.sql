CREATE DATABASE IF NOT EXISTS zegowska_szama
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_polish_ci;

USE zegowska_szama;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  login VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  category VARCHAR(50) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  is_promo TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, category, description, price, stock, is_promo)
SELECT 'Kanapka z szynka', 'kanapki', 'Swieze pieczywo, szynka, ser, salata.', 4.50, 12, 0
WHERE NOT EXISTS (SELECT 1 FROM products);

INSERT INTO products (name, category, description, price, stock, is_promo)
SELECT 'Sok pomaranczowy', 'napoje', '100% sok tloczony, 300ml.', 2.50, 3, 1
WHERE (SELECT COUNT(*) FROM products) = 1;

INSERT INTO products (name, category, description, price, stock, is_promo)
SELECT 'Baton czekoladowy', 'slodycze', 'Klasyczny baton mleczny 45g.', 2.00, 20, 0
WHERE (SELECT COUNT(*) FROM products) = 2;

INSERT INTO products (name, category, description, price, stock, is_promo)
SELECT 'Precel z sola', 'przekaski', 'Chrupiący precel z gruboziarnista sola.', 1.80, 10, 0
WHERE (SELECT COUNT(*) FROM products) = 3;
