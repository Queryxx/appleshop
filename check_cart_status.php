<?php
include 'conn.php';
session_start();

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 1;
$cart_count = $conn->query("SELECT COUNT(DISTINCT product_id) as count FROM cart WHERE user_id = $user_id")
    ->fetch_assoc()['count'] ?? 0;

echo json_encode(['count' => $cart_count]);

?>