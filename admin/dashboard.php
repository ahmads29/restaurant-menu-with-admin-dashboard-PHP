<?php
require_once '../includes/auth.php';
requireLogin();

$conn = getDBConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_category':
                $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
                $stmt->execute([$_POST['category_name']]);
                echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
                break;
                
            case 'edit_category':
                $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
                $stmt->execute([$_POST['category_name'], $_POST['category_id']]);
                echo json_encode(['success' => true]);
                break;
                
            case 'delete_category':
                $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute([$_POST['category_id']]);
                echo json_encode(['success' => true]);
                break;
                
            case 'create':
                if (isset($_POST['name'], $_POST['category_id'], $_POST['price'])) {
                    try {
                        $stmt = $conn->prepare("INSERT INTO menu_items (name, category_id, price, description) VALUES (?, ?, ?, ?)");
                        $stmt->execute([
                            $_POST['name'],
                            $_POST['category_id'],
                            $_POST['price'],
                            $_POST['description'] ?? null
                        ]);
                        echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
                    } catch (PDOException $e) {
                        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                }
                exit;

            case 'update':
                if (isset($_POST['id'], $_POST['name'], $_POST['category_id'], $_POST['price'])) {
                    try {
                        $stmt = $conn->prepare("UPDATE menu_items 
                                               SET name = ?, category_id = ?, price = ?, description = ?
                                               WHERE id = ?");
                        $stmt->execute([
                            $_POST['name'],
                            $_POST['category_id'],
                            $_POST['price'],
                            $_POST['description'] ?? null,
                            $_POST['id']
                        ]);
                        echo json_encode(['success' => true]);
                    } catch (PDOException $e) {
                        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                }
                exit;
                
            case 'delete':
                if (isset($_POST['id'])) {
                    try {
                        $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        echo json_encode(['success' => true]);
                    } catch (PDOException $e) {
                        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
                }
                exit;
        }
    }
}

// Handle GET requests for item details
if (isset($_GET['get_item']) && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($item);
    exit;
}

// Fetch categories and items
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$items = $conn->query("SELECT m.*, c.name as category_name FROM menu_items m JOIN categories c ON m.category_id = c.id ORDER BY c.name, m.name")->fetchAll(PDO::FETCH_ASSOC);
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
            <a href="/" class="logo-link">
                <img src="../uploads/dark.png" alt="Devzur Logo" class="admin-header-logo">
            </a>
            <div class="admin-actions">
                <button class="admin-btn" onclick="showAddModal()">
                    <i class="fas fa-plus"></i> Add New Item
                </button>
                <a href="manage_categories.php" class="admin-btn">
                    <i class="fas fa-tags"></i> Manage Categories
                </a>
                <a href="../index.php" class="admin-btn">
                    <i class="fas fa-eye"></i> View Menu
                </a>
            </div>
        </div>

        <div class="admin-grid">
            <div class="admin-card">
                <div class="card-header">
                    <h2>Menu Items</h2>
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search items..." onkeyup="searchItems()">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="table-actions">
                                        <button class="admin-btn" onclick="editItem(<?php echo $item['id']; ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="admin-btn btn-danger" onclick="deleteItem(<?php echo $item['id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="itemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Add New Item</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="itemForm" class="admin-form" onsubmit="return handleSubmit(event)">
                <input type="hidden" id="itemId" name="id">
                <input type="hidden" name="action" id="formAction" value="create">
                <div class="form-group">
                    <label for="itemName">Name</label>
                    <input type="text" id="itemName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="itemCategory">Category</label>
                    <select id="itemCategory" name="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="itemPrice">Price</label>
                    <input type="number" id="itemPrice" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="itemDescription">Description</label>
                    <textarea id="itemDescription" name="description" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="admin-btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="admin-btn" id="submitButton">Add Item</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Item';
            document.getElementById('formAction').value = 'create';
            document.getElementById('submitButton').textContent = 'Add Item';
            document.getElementById('itemForm').reset();
            document.getElementById('itemId').value = '';
            document.getElementById('itemModal').style.display = 'flex';
        }

        function editItem(id) {
            document.getElementById('modalTitle').textContent = 'Edit Item';
            document.getElementById('formAction').value = 'update';
            document.getElementById('submitButton').textContent = 'Save Changes';
            
            fetch(`dashboard.php?get_item=1&id=${id}`)
                .then(response => response.json())
                .then(item => {
                    document.getElementById('itemId').value = item.id;
                    document.getElementById('itemName').value = item.name;
                    document.getElementById('itemCategory').value = item.category_id;
                    document.getElementById('itemPrice').value = item.price;
                    document.getElementById('itemDescription').value = item.description;
                    document.getElementById('itemModal').style.display = 'flex';
                });
        }

        function closeModal() {
            document.getElementById('itemModal').style.display = 'none';
        }

        function handleSubmit(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            fetch('dashboard.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
            
            return false;
        }

        function deleteItem(id) {
            if (confirm('Are you sure you want to delete this item?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                
                fetch('dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        function searchItems() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('itemsTableBody');
            const tr = table.getElementsByTagName('tr');

            for (let i = 0; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[0];
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }
    </script>
    
    <footer class="admin-footer">
        <p>© 2025 <a href="https://devzur.com" target="_blank">Devzur</a>. All rights reserved.</p>
    </footer>
</body>
</html> 