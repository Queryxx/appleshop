<?php
include 'conn.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? 1; // Default to 1 for testing
$cart_id = $_POST['cart_id'] ?? null;
$action = $_POST['action'] ?? '';

if (!$cart_id || !$action) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

switch ($action) {
    case 'increase':
        $sql = "UPDATE cart SET quantity = quantity + 1 WHERE cart_id = ? AND user_id = ?";
        break;
    case 'decrease':
        $sql = "UPDATE cart SET quantity = GREATEST(1, quantity - 1) WHERE cart_id = ? AND user_id = ?";
        break;
    case 'remove':
        $sql = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating cart']);
}