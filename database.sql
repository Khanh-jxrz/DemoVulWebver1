-- Tạo database
CREATE DATABASE IF NOT EXISTS vuln_shop;
USE vuln_shop;

-- Bảng users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    avatar VARCHAR(255) DEFAULT 'default.jpg',
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng comments
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    user_id INT,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Bảng user_profiles (cho IDOR)
CREATE TABLE user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    secret_note TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert dữ liệu mẫu
INSERT INTO users (username, password, email, full_name, role) VALUES
('admin', 'admin123', 'admin@vulnshop.com', 'Administrator', 'admin'),
('user1', 'password123', 'user1@example.com', 'Nguyen Van A', 'user'),
('user2', 'password456', 'user2@example.com', 'Tran Thi B', 'user');

INSERT INTO products (name, description, price, image) VALUES
('MacBook Air M2', 'Laptop mỏng nhẹ dùng chip Apple M2', 28000000, 'macbook_air_m2.jpg'),
('MacBook Pro M3', 'Laptop hiệu năng cao cho lập trình và đồ họa', 42000000, 'macbook_pro_m3.jpg'),
('Asus ROG Strix G16', 'Laptop gaming hiệu năng cao', 35000000, 'rog_strix.jpg'),
('HP Spectre x360', 'Laptop doanh nhân cao cấp xoay gập', 33000000, 'hp_spectre.jpg'),
('Lenovo ThinkPad X1 Carbon', 'Laptop doanh nhân bền bỉ', 39000000, 'thinkpad_x1.jpg'),

('iPad Pro M4', 'Máy tính bảng mạnh mẽ cho công việc sáng tạo', 32000000, 'ipad_pro.jpg'),
('iPad Air 6', 'Tablet mỏng nhẹ, hiệu năng tốt', 18000000, 'ipad_air.jpg'),
('Samsung Galaxy Tab S9', 'Tablet Android cao cấp', 21000000, 'tab_s9.jpg'),

('Apple Watch Series 9', 'Đồng hồ thông minh Apple', 11000000, 'apple_watch.jpg'),
('Samsung Galaxy Watch 6', 'Smartwatch Android', 8500000, 'galaxy_watch.jpg'),

('AirPods Pro 2', 'Tai nghe chống ồn chủ động', 6500000, 'airpods_pro.jpg'),
('Sony WH-1000XM5', 'Tai nghe chống ồn cao cấp', 9000000, 'sony_xm5.jpg'),

('Logitech MX Master 3S', 'Chuột không dây cho dân văn phòng', 2500000, 'mx_master.jpg'),
('Keychron K8 Pro', 'Bàn phím cơ không dây', 3200000, 'keychron_k8.jpg'),

('LG UltraFine 5K', 'Màn hình độ phân giải cao cho designer', 45000000, 'lg_5k.jpg'),
('Dell UltraSharp U2723QE', 'Màn hình 4K cho công việc chuyên nghiệp', 19000000, 'dell_4k.jpg');


INSERT INTO user_profiles (user_id, phone, address, secret_note) VALUES
(1, '0123456789', '123 Admin Street', 'Admin secret: flag3sqli_'),
(2, '0987654321', '456 User Street', 'User 1 personal info'),
(3, '0912345678', '789 Customer Ave', 'User 2 personal info');
