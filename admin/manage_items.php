<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../includes/db.php';
require_once 'check_auth.php';

echo '<pre>DEBUG SESSION: ';
print_r($_SESSION);
echo "</pre>\n";

$conn = getDBConnection();
if (!$conn) {
    die('<pre>DB connection failed</pre>');
}

// Test DB query
try {
    $test = $conn->query('SELECT 1')->fetch();
    echo '<pre>DB test query result: ';
    print_r($test);
    echo "</pre>\n";
} catch (Exception $e) {
    die('<pre>DB test query error: ' . $e->getMessage() . '</pre>');
}

// echo '<pre>Passed all checks. Remove debug to continue.</pre>';
// die();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $conn->prepare("INSERT INTO menu_items (name, description, price, category_id, image_path) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['price'],
                    $_POST['category_id'],
                    $_POST['image_path']
                ]);
                header('Location: manage_items.php?success=added');
                exit;
                break;

            case 'edit':
                $stmt = $conn->prepare("UPDATE menu_items SET name = ?, description = ?, price = ?, category_id = ?, image_path = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['price'],
                    $_POST['category_id'],
                    $_POST['image_path'],
                    $_POST['item_id']
                ]);
                header('Location: manage_items.php?success=updated');
                exit;
                break;

            case 'delete':
                $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
                $stmt->execute([$_POST['item_id']]);
                header('Location: manage_items.php?success=deleted');
                exit;
                break;
        }
    }
}

// Get all menu items with their categories
$items = $conn->query("
    SELECT m.*, c.name as category_name 
    FROM menu_items m 
    JOIN categories c ON m.category_id = c.id 
    ORDER BY c.name, m.name
")->fetchAll(PDO::FETCH_ASSOC);

// Get all categories for the form
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu Items - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
            padding: 2rem 1rem;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .close-modal {
            position: absolute;
            right: 1rem;
            top: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .item-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }

        .admin-table td {
            vertical-align: middle;
            padding: 0.75rem;
        }

        .table-actions {
            display: flex;
            gap: 0.5rem;
        }

        .success-message {
            margin: 1rem 0;
        }

        .admin-form .form-group {
            margin-bottom: 1.5rem;
        }

        .admin-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .admin-form input,
        .admin-form select,
        .admin-form textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 1rem;
        }

        .admin-form textarea {
            min-height: 100px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Manage Menu Items</h1>
            <div class="admin-actions">
                <button class="admin-btn" onclick="showAddModal()">
                    <i class="fas fa-plus"></i> Add New Item
                </button>
                <a href="index.php" class="admin-btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <?php
                switch ($_GET['success']) {
                    case 'added':
                        echo 'Menu item added successfully!';
                        break;
                    case 'updated':
                        echo 'Menu item updated successfully!';
                        break;
                    case 'deleted':
                        echo 'Menu item deleted successfully!';
                        break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="admin-card">
            <?php if (empty($items)): ?>
                <p class="no-items">No menu items found. Click "Add New Item" to create one.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($item['image_path']) ? htmlspecialchars($item['image_path']) : '../uploads/default.jpg'; ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         class="item-thumbnail"
                                         onerror="this.onerror=null;this.src='../uploads/default.jpg';">
                                </td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['description']); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td class="table-actions">
                                    <button class="btn btn-primary" onclick='editItem(<?php echo json_encode($item); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteItem(<?php echo $item['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="itemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Item</h2>
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="itemForm" method="POST" class="admin-form">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="item_id" id="itemId">
                
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="image_path">Image Path</label>
                    <input type="text" id="image_path" name="image_path" placeholder="uploads/your-image.jpg">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Delete Item</h2>
                <button type="button" class="close-modal" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this item? This action cannot be undone.</p>
            </div>
            <form method="POST" class="admin-form">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="item_id" id="deleteItemId">
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editItem(item) {
            document.getElementById('modalTitle').textContent = 'Edit Item';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('itemId').value = item.id;
            document.getElementById('name').value = item.name;
            document.getElementById('category_id').value = item.category_id;
            document.getElementById('description').value = item.description;
            document.getElementById('price').value = item.price;
            document.getElementById('image_path').value = item.image_path || '';
            document.getElementById('itemModal').style.display = 'flex';
        }

        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Item';
            document.getElementById('formAction').value = 'add';
            document.getElementById('itemId').value = '';
            document.getElementById('itemForm').reset();
            document.getElementById('itemModal').style.display = 'flex';
        }

        function deleteItem(itemId) {
            document.getElementById('deleteItemId').value = itemId;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('itemModal').style.display = 'none';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Auto-hide success message
        const successMessage = document.querySelector('.success-message');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html> 