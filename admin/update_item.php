<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$required_fields = ['id', 'name', 'category_id', 'price'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

try {
    $conn = getDBConnection();
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