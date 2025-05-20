<?php
require_once '../includes/db.php';
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

$conn = getDBConnection();

// Get recent orders
$stmt = $conn->query("
    SELECT o.*, 
           COUNT(oi.id) as total_items,
           GROUP_CONCAT(CONCAT(oi.quantity, 'x ', mi.name) SEPARATOR ', ') as items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total orders and revenue
$stats = $conn->query("
    SELECT 
        COUNT(*) as total_orders,
        SUM(total_amount) as total_revenue
    FROM orders
")->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Restaurant Menu</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Admin Dashboard</h1>
            <div class="admin-actions">
                <a href="manage_categories.php" class="admin-btn">
                    <i class="fas fa-list"></i> Manage Categories
                </a>
                <a href="manage_items.php" class="admin-btn">
                    <i class="fas fa-utensils"></i> Manage Menu Items
                </a>
                <a href="orders.php" class="admin-btn">
                    <i class="fas fa-shopping-bag"></i> Manage Orders
                </a>
                <a href="logout.php" class="admin-btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card stats-card">
                <div class="stat-item">
                    <i class="fas fa-shopping-cart"></i>
                    <div class="stat-details">
                        <h3>Total Orders</h3>
                        <p><?php echo number_format($stats['total_orders']); ?></p>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-dollar-sign"></i>
                    <div class="stat-details">
                        <h3>Total Revenue</h3>
                        <p>$<?php echo number_format($stats['total_revenue'], 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <h2><i class="fas fa-clock"></i> Recent Orders</h2>
                <div class="recent-orders">
                    <?php if (empty($recentOrders)): ?>
                        <p class="no-orders">No orders yet</p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                        </td>
                                        <td>
                                            <div class="order-items-list">
                                                <?php echo htmlspecialchars($order['items']); ?>
                                            </div>
                                        </td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo strtolower($order['status'] ?? 'pending'); ?>">
                                                <?php echo htmlspecialchars($order['status'] ?? 'Pending'); ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <a href="orders.php?id=<?php echo $order['id']; ?>" class="btn btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="view-all">
                            <a href="orders.php" class="admin-btn">View All Orders</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 