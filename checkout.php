<?php
require_once 'includes/cart.php';
require_once 'includes/db.php';

$cart = new Cart();
$cartCount = $cart->getItemCount();
$conn = getDBConnection();

// Redirect to cart if empty
if (empty($cart->getItems())) {
    header('Location: cart.php');
    exit;
}

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['customer_name'], $_POST['customer_email'], $_POST['customer_phone'], $_POST['delivery_address'])) {
        try {
            $conn->beginTransaction();
            
            // Create order
            $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, delivery_address, total_amount) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['customer_name'],
                $_POST['customer_email'],
                $_POST['customer_phone'],
                $_POST['delivery_address'],
                $cart->getTotal()
            ]);
            
            $orderId = $conn->lastInsertId();
            
            // Add order items
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart->getItems() as $item) {
                $stmt->execute([
                    $orderId,
                    $item['id'],
                    $item['quantity'],
                    $item['price']
                ]);
            }
            
            $conn->commit();
            
            // Clear the cart
            $cart->clear();
            
            // Redirect to order confirmation
            header('Location: order-confirmation.php?id=' . $orderId);
            exit;
            
        } catch (Exception $e) {
            $conn->rollBack();
            $error = "An error occurred while processing your order. Please try again.";
        }
    }
}

$cartItems = $cart->getItems();
$total = $cart->getTotal();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Restaurant Menu</title>
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
        <div class="checkout-container">
            <h2>Checkout</h2>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="checkout-grid">
                <div class="order-details">
                    <h3>Order Summary</h3>
                    <div class="order-items">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="order-item">
                                <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                <span class="item-quantity">×<?php echo $item['quantity']; ?></span>
                                <span class="item-price">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="order-total">
                        <strong>Total:</strong>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
                
                <form method="POST" class="checkout-form">
                    <h3>Delivery Details</h3>
                    <div class="form-group">
                        <label for="customer_name">Full Name</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_email">Email</label>
                        <input type="email" id="customer_email" name="customer_email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_phone">Phone</label>
                        <input type="tel" id="customer_phone" name="customer_phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="delivery_address">Delivery Address</label>
                        <textarea id="delivery_address" name="delivery_address" rows="3" required></textarea>
                    </div>
                    
                    <div class="payment-method">
                        <h3>Payment Method</h3>
                        <div class="payment-option">
                            <input type="radio" id="cash" name="payment_method" value="cash" checked>
                            <label for="cash">Cash on Delivery</label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                        <button type="submit" class="btn btn-primary">Place Order</button>
                    </div>
                </form>
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