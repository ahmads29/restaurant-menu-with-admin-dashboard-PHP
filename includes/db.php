<?php
// Database configuration
define('DB_HOST', 'localhost:3327');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'restaurant_menu1');

// Create initial MySQL connection without database
function getInitialConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Create database connection with specific database
function getDBConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        // If database doesn't exist, create it
        if ($e->getCode() == 1049) {
            $conn = getInitialConnection();
            $conn->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
            $conn->exec("USE " . DB_NAME);
            return $conn;
        }
        die("Connection failed: " . $e->getMessage());
    }
}

// Initialize database tables if they don't exist
function initDatabase() {
    $conn = getDBConnection();
    
    // Create admins table
    $conn->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )");
    
    // Create categories table
    $conn->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE
    )");
    
    // Create menu_items table
    $conn->exec("CREATE TABLE IF NOT EXISTS menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image_path VARCHAR(255),
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    )");
    
    // Create default admin if not exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM admins");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hashedPassword]);
    }
}

// Initialize database on first run
initDatabase();
?> 