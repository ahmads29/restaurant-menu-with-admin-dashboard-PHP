<?php
require_once 'includes/db.php';
require_once 'includes/cart.php';

$conn = getDBConnection();
$cart = new Cart();
$cartCount = $cart->getItemCount();

// Fetch categories and items
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$items = $conn->query("SELECT m.*, c.name as category_name FROM menu_items m JOIN categories c ON m.category_id = c.id ORDER BY c.name, m.name")->fetchAll(PDO::FETCH_ASSOC);

// Group items by category
$itemsByCategory = [];
foreach ($items as $item) {
    $itemsByCategory[$item['category_name']][] = $item;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Menu</title>
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
        <div class="category-tabs-container">
            <button class="scroll-button scroll-left" onclick="scrollCategories('left')">←</button>
            <div class="category-tabs">
                <div class="category-tab active" data-category="all">All Items</div>
                <?php foreach ($categories as $category): ?>
                    <div class="category-tab" data-category="<?php echo htmlspecialchars($category['name']); ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="scroll-button scroll-right" onclick="scrollCategories('right')">→</button>
        </div>
        
        <div class="menu-grid">
            <?php foreach ($items as $item): ?>
                <div class="menu-item" data-category="<?php echo htmlspecialchars($item['category_name']); ?>">
                    <?php if ($item['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                             onerror="this.src='uploads/default.jpg'">
                    <?php endif; ?>
                    <div class="menu-item-content">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="description"><?php echo htmlspecialchars($item['description']); ?></p>
                        <p class="price">$<?php echo number_format($item['price'], 2); ?></p>
                        <form action="cart.php" method="POST" class="add-to-cart-form">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="btn btn-primary add-to-cart-btn">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryTabs = document.querySelectorAll('.category-tab');
            const menuItems = document.querySelectorAll('.menu-item');
            const categoryTabsContainer = document.querySelector('.category-tabs');
            
            categoryTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Update active tab
                    categoryTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    const category = this.dataset.category;
                    
                    // Show/hide menu items
                    menuItems.forEach(item => {
                        if (category === 'all' || item.dataset.category === category) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

            // Update scroll buttons visibility
            function updateScrollButtons() {
                const leftButton = document.querySelector('.scroll-left');
                const rightButton = document.querySelector('.scroll-right');
                const scrollLeft = categoryTabsContainer.scrollLeft;
                const maxScroll = categoryTabsContainer.scrollWidth - categoryTabsContainer.clientWidth;

                leftButton.style.display = scrollLeft > 0 ? 'flex' : 'none';
                rightButton.style.display = scrollLeft < maxScroll ? 'flex' : 'none';
            }

            // Initial check
            updateScrollButtons();

            // Update on scroll
            categoryTabsContainer.addEventListener('scroll', updateScrollButtons);

            // Update on resize
            window.addEventListener('resize', updateScrollButtons);
        });

        function scrollCategories(direction) {
            const container = document.querySelector('.category-tabs');
            const scrollAmount = 200; // Adjust this value to change scroll distance
            
            if (direction === 'left') {
                container.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            } else {
                container.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            }
        }
    </script>
    
    <footer>
        <div class="container">
            <p>© 2025 <a href="https://devzur.com" target="_blank">Devzur</a>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 