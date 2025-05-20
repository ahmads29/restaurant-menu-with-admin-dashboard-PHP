<?php
session_start();
require_once 'db.php';

class Cart {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    public function addItem($menuItemId, $quantity = 1) {
        // Get menu item details
        $stmt = $this->conn->prepare("SELECT * FROM menu_items WHERE id = ?");
        $stmt->execute([$menuItemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$item) {
            return false;
        }
        
        if (isset($_SESSION['cart'][$menuItemId])) {
            $_SESSION['cart'][$menuItemId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$menuItemId] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $quantity
            ];
        }
        
        return true;
    }
    
    public function removeItem($menuItemId) {
        if (isset($_SESSION['cart'][$menuItemId])) {
            unset($_SESSION['cart'][$menuItemId]);
            return true;
        }
        return false;
    }
    
    public function updateQuantity($menuItemId, $quantity) {
        if (isset($_SESSION['cart'][$menuItemId])) {
            if ($quantity <= 0) {
                return $this->removeItem($menuItemId);
            }
            $_SESSION['cart'][$menuItemId]['quantity'] = $quantity;
            return true;
        }
        return false;
    }
    
    public function getItems() {
        return $_SESSION['cart'];
    }
    
    public function getTotal() {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
    
    public function clear() {
        $_SESSION['cart'] = [];
    }
    
    public function getItemCount() {
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }
} 