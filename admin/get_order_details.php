<?php
session_start();
include '../conn.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

try {
    // Get order details with customer information
    $order_query = "SELECT o.*, u.name, u.email, u.phone_number
                   FROM orders o
                   JOIN users u ON o.user_id = u.user_id
                   WHERE o.order_id = ?";
    $stmt = mysqli_prepare($conn, $order_query);
    mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
    mysqli_stmt_execute($stmt);
    $order_result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($order_result);

    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
        exit;
    }

    // Get order items with product details
    $items_query = "SELECT oi.*, p.product_name, p.image, p.price as unit_price
                   FROM order_items oi
                   JOIN products p ON oi.product_id = p.product_id
                   WHERE oi.order_id = ?";
    $stmt = mysqli_prepare($conn, $items_query);
    mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
    mysqli_stmt_execute($stmt);
    $items_result = mysqli_stmt_get_result($stmt);
    $items = [];
    
    while ($item = mysqli_fetch_assoc($items_result)) {
        $items[] = [
            'product_name' => $item['product_name'],
            'image' => $item['image'],
            'quantity' => $item['quantity'],
            'price' => number_format($item['unit_price'], 2),
            'subtotal' => number_format($item['unit_price'] * $item['quantity'], 2)
        ];
    }

    // Format the response
    $response = [
        'order' => [
            'order_id' => $order['order_id'],
            'created_at' => date('M d, Y h:i A', strtotime($order['created_at'])),
            'status' => $order['status'],
            'payment_status' => $order['payment_status'],
            'total_amount' => number_format($order['total_amount'], 2),
            'customer' => [
                'name' => $order['name'],
                'email' => $order['email'],
                'phone' => $order['phone_number']
            ]
        ],
        'items' => $items
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}