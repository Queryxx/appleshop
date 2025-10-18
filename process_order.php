<?php
include 'conn.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $conn->begin_transaction();

    $user_id = $_SESSION['user_id'] ?? 1;

    // Add payment_method to the order insertion
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, phone_number, email, payment_method) 
VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssss", $user_id, $total_amount, $_POST['address'], $_POST['phone'], $_POST['email'], $_POST['payment_method']);

    // Handle payment proof upload if exists
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'payment_proofs/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
        $filename = 'payment_' . $order_id . '.' . $file_extension;
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target_path)) {
            // Update order with payment proof
            $conn->query("UPDATE orders SET payment_proof = '$filename' WHERE order_id = $order_id");
        }
    }
    // Calculate total and insert order items
    $cart_sql = "SELECT c.*, p.price, p.discount FROM cart c 
                 JOIN products p ON c.product_id = p.product_id 
                 WHERE c.user_id = ?";
    $cart_stmt = $conn->prepare($cart_sql);
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();

    $total_amount = 0;
    $order_items = [];

    while ($item = $cart_result->fetch_assoc()) {
        $price = $item['price'];
        if (!is_null($item['discount']) && $item['discount'] > 0) {
            $discount_amount = $price * ($item['discount'] / 100);
            $price = $price - $discount_amount;
        }
        $item_total = $price * $item['quantity'];
        $total_amount += $item_total;
        $order_items[] = $item;
    }

    $stmt->execute();
    $order_id = $conn->insert_id;

    // Insert order items
    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($order_items as $item) {
        $price = $item['price'];
        if (!is_null($item['discount']) && $item['discount'] > 0) {
            $discount_amount = $price * ($item['discount'] / 100);
            $price = $price - $discount_amount;
        }
        $item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $price);
        $item_stmt->execute();
    }

    // Clear cart
    $conn->query("DELETE FROM cart WHERE user_id = $user_id");

    $conn->commit();
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
