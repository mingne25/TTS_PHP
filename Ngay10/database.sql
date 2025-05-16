CREATE DATABASE IF NOT EXISTS ecommerce;
USE ecommerce;

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    category VARCHAR(50),
    brand VARCHAR(50),
    image_url VARCHAR(255)
);

CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    user_name VARCHAR(100),
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE polls (
    id INT PRIMARY KEY AUTO_INCREMENT,
    option_name VARCHAR(50),
    votes INT DEFAULT 0
);

-- Insert sample data
INSERT INTO products VALUES
(1, 'iPhone 13', 'Latest Apple smartphone', 999.99, 50, 'Electronics', 'Apple', 'assets/images/iphone13.jpg'),
(2, 'Samsung TV', '4K Smart TV', 799.99, 30, 'Electronics', 'Samsung', 'assets/images/samsung-tv.jpg'),
(3, 'Nike Air Max', 'Running shoes', 129.99, 100, 'Fashion', 'Nike', 'assets/images/nike-air.jpg');

INSERT INTO polls (option_name, votes) VALUES
('Interface', 0),
('Speed', 0),
('Customer Service', 0);