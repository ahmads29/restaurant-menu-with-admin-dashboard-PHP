<?php
require_once 'includes/cart.php';
$cart = new Cart();
$cartCount = $cart->getItemCount();

// Handle POST requests for updating cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                if (isset($_POST['item_id'])) {
                    $cart->addItem($_POST['item_id'], 1);
                }
                break;
            case 'update':
                if (isset($_POST['item_id'], $_POST['quantity'])) {
                    $cart->updateQuantity($_POST['item_id'], (int)$_POST['quantity']);
                }
                break;
            case 'remove':
                if (isset($_POST['item_id'])) {
                    $cart->removeItem($_POST['item_id']);
                }
                break;
        }
        // Redirect to prevent form resubmission
        header('Location: cart.php');
        exit;
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
    <title>Shopping Cart - Restaurant Menu</title>
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
        <div class="cart-container">
            <h2><i class="fas fa-shopping-cart"></i> Your Cart</h2>
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <p><i class="fas fa-box-open fa-2x" style="color:#e5e7eb;"></i></p>
                    <p>Your cart is empty</p>
                    <a href="index.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="price">$<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                            <div class="item-actions">
                                <form method="POST" class="quantity-form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" onchange="this.form.submit()">
                                </form>
                                <form method="POST" class="remove-form">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="remove-btn" title="Remove">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <div class="item-total">
                                    $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="cart-summary">
                    <div class="total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="cart-actions">
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
                        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>© 2025 <a href="https://devzur.com" target="_blank">Devzur</a>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 