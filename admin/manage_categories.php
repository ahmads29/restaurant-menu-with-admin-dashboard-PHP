<?php
require_once '../includes/db.php';
$conn = getDBConnection();

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                if (isset($_POST['name'])) {
                    try {
                        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
                        $stmt->execute([$_POST['name']]);
                        echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
                    } catch (PDOException $e) {
                        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Category name is required']);
                }
                exit;

            case 'update':
                if (isset($_POST['id'], $_POST['name'])) {
                    try {
                        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
                        $stmt->execute([$_POST['name'], $_POST['id']]);
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
                        // Check if category has items
                        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM menu_items WHERE category_id = ?");
                        $checkStmt->execute([$_POST['id']]);
                        $itemCount = $checkStmt->fetchColumn();
                        
                        if ($itemCount > 0) {
                            echo json_encode(['success' => false, 'message' => 'Cannot delete category with existing items']);
                            exit;
                        }
                        
                        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        echo json_encode(['success' => true]);
                    } catch (PDOException $e) {
                        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Category ID is required']);
                }
                exit;
        }
    }
}

// Fetch all categories
$categories = $conn->query("SELECT c.*, COUNT(m.id) as item_count 
                          FROM categories c 
                          LEFT JOIN menu_items m ON c.id = m.category_id 
                          GROUP BY c.id 
                          ORDER BY c.name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Restaurant Menu</title>
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
                    <i class="fas fa-plus"></i> Add Category
                </button>
                <a href="dashboard.php" class="admin-btn">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <div class="admin-grid">
            <div class="admin-card">
                <div class="card-header">
                    <h2>Categories</h2>
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search categories..." onkeyup="searchCategories()">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Items Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo $category['item_count']; ?></td>
                                    <td class="table-actions">
                                        <button class="admin-btn" onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="admin-btn btn-danger" onclick="deleteCategory(<?php echo $category['id']; ?>)">
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
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Add Category</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="categoryForm" class="admin-form" onsubmit="return handleSubmit(event)">
                <input type="hidden" id="categoryId" name="id">
                <input type="hidden" name="action" id="formAction" value="create">
                <div class="form-group">
                    <label for="categoryName">Category Name</label>
                    <input type="text" id="categoryName" name="name" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="admin-btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="admin-btn" id="submitButton">Add Category</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Category';
            document.getElementById('formAction').value = 'create';
            document.getElementById('submitButton').textContent = 'Add Category';
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryId').value = '';
            document.getElementById('categoryModal').style.display = 'flex';
        }

        function editCategory(id, name) {
            document.getElementById('modalTitle').textContent = 'Edit Category';
            document.getElementById('formAction').value = 'update';
            document.getElementById('submitButton').textContent = 'Save Changes';
            document.getElementById('categoryId').value = id;
            document.getElementById('categoryName').value = name;
            document.getElementById('categoryModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('categoryModal').style.display = 'none';
        }

        function handleSubmit(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            fetch('manage_categories.php', {
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

        function deleteCategory(id) {
            if (confirm('Are you sure you want to delete this category?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                
                fetch('manage_categories.php', {
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

        function searchCategories() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('categoriesTableBody');
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