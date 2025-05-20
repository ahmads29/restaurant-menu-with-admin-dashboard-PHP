<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
    exit;
}

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
$stmt->execute([$_GET['id']]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    echo json_encode($item);
} else {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
} 