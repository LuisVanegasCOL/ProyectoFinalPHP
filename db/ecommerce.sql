-- Eliminar la base de datos si ya existe
DROP DATABASE IF EXISTS mini_ecommerce;

-- Crear la base de datos
CREATE DATABASE mini_ecommerce;
USE mini_ecommerce;

-- Tabla de administradores
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255)
);

-- Tabla de productos
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    image VARCHAR(255),
    category VARCHAR(50)
);

-- Tabla de pedidos
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    address TEXT,
    cart TEXT,
    total DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Usuario admin (usuario: admin, contraseña: admin123)
INSERT INTO admin (username, password) VALUES (
  'admin',
  '$2y$10$wH6QwQwQwQwQwQwQwQwQOeQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw'
);

-- Productos de ejemplo con imágenes CDN
INSERT INTO products (name, description, price, image, category) VALUES
('Taza artesanal', 'Taza hecha a mano en cerámica.', 12.50, 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80', 'Cerámica'),
('Plato decorativo', 'Plato decorativo pintado a mano.', 18.00, 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80', 'Decoración'),
('Jarrón pequeño', 'Jarrón de cerámica para flores.', 15.00, 'https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=400&q=80', 'Cerámica'); 