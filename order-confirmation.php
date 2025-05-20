<?php
require_once 'includes/db.php';
require_once 'includes/cart.php';
session_start();

$conn = getDBConnection();
$cart = new Cart();
$cartCount = $cart->getItemCount();

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$orderId = (int)$_GET['id'];

// Get order details
$stmt = $conn->prepare("
    SELECT o.*, 
           GROUP_CONCAT(CONCAT(oi.quantity, 'x ', mi.name) SEPARATOR ', ') as items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
    WHERE o.id = ?
    GROUP BY o.id
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// If order not found, redirect to home
if (!$order) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Restaurant Menu</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="/" class="logo-link">
                    <img src="uploads/dark.png" alt="Devzur Logo" class="header-logo">
                </a>
                <a href="cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-count"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </header>
    
    <main class="container">
        <div class="confirmation-container">
            <div class="confirmation-card">
                <div class="confirmation-header">
                    <i class="fas fa-check-circle"></i>
                    <h2>Order Confirmed!</h2>
                    <p>Thank you for your order. We'll start preparing it right away!</p>
                </div>
                
                <div class="order-details">
                    <h3>Order #<?php echo $orderId; ?></h3>
                    
                    <div class="detail-group">
                        <label>Order Status:</label>
                        <span class="status-badge">
                            <?php echo htmlspecialchars($order['status'] ?? 'Pending'); ?>
                        </span>
                    </div>
                    
                    <div class="detail-group">
                        <label>Customer Details:</label>
                        <p><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></p>
                        <p><?php echo htmlspecialchars($order['customer_email']); ?></p>
                        <p><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                    </div>
                    
                    <div class="detail-group">
                        <label>Delivery Address:</label>
                        <p><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                    </div>
                    
                    <div class="detail-group">
                        <label>Order Items:</label>
                        <p><?php echo htmlspecialchars($order['items']); ?></p>
                    </div>
                    
                    <div class="detail-group total">
                        <label>Total Amount:</label>
                        <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
                
                <div class="confirmation-actions">
                    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>© 2025 <a href="https://devzur.com" target="_blank">Devzur</a>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 