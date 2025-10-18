<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $payment_method = $_POST['payment_method'];
    $quantity = max(1, (int)$_POST['quantity']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Handle GCash payment proof upload
        $payment_proof = null;
        if ($payment_method === 'gcash' && isset($_FILES['payment_proof'])) {
            $upload_dir = 'payment_proofs/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $payment_proof = uniqid() . '_' . $_FILES['payment_proof']['name'];
            $target_path = $upload_dir . $payment_proof;

            if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target_path)) {
                throw new Exception('Failed to upload payment proof');
            }
        }

        // Create order
        $order_sql = "INSERT INTO orders (user_id, status, payment_method, payment_proof, created_at) 
                     VALUES (?, ?, ?, ?, NOW())";
        $status = $payment_method === 'cod' ? 'pending' : 'awaiting_payment';
        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bind_param("isss", $user_id, $status, $payment_method, $payment_proof);
        $order_stmt->execute();

        $order_id = $conn->insert_id;

        // Get product details and insert order item
        $product_sql = "SELECT price, discount FROM products WHERE product_id = ?";
        $product_stmt = $conn->prepare($product_sql);
        $product_stmt->bind_param("i", $product_id);
        $product_stmt->execute();
        $product = $product_stmt->get_result()->fetch_assoc();

        // Calculate final price with discount
        $price = $product['price'];
        if (!is_null($product['discount']) && $product['discount'] > 0) {
            $discount_amount = $price * ($product['discount'] / 100);
            $price = $price - $discount_amount;
        }

        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                     VALUES (?, ?, ?, ?)";
        $item_stmt = $conn->prepare($item_sql);
        $item_stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
        $item_stmt->execute();

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true, 'order_id' => $order_id]);
    } catch (Exception $e) {
        $conn->rollback();
        // Delete uploaded file if it exists
        if (isset($target_path) && file_exists($target_path)) {
            unlink($target_path);
        }
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>