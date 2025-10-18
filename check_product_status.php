<?php
include 'conn.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT product_id, status FROM products";
    $result = $conn->query($sql);
    
    $products = array();
    while($row = $result->fetch_assoc()) {
        $products[] = array(
            'product_id' => $row['product_id'],
            'status' => $row['status']
        );
    }
    
    echo json_encode($products);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>