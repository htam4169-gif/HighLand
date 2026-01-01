<?php
session_start();
require_once 'db.php';

$conn->query("SET FOREIGN_KEY_CHECKS=0");

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data) {
    $orderType = $data['orderType'];
    $pager = empty($data['pager']) ? 0 : $data['pager']; 
    $cart = $data['cart'];
    
   
    $totalAmount = 0;
    foreach ($cart as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }

   
    $stmt = $conn->prepare("INSERT INTO orders (order_type, pager_number, total_amount, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sid", $orderType, $pager, $totalAmount);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        $stmt->close();

      
        $stmt_detail = $conn->prepare("INSERT INTO order_details (order_id, product_id, product_name, quantity, price_at_time, note) VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($cart as $item) {
            
            $pid = is_numeric($item['id']) ? $item['id'] : 0;
            
            $name = $item['name'];
            $qty = $item['quantity'];
            $price = $item['price'];
            $note = isset($item['note']) ? $item['note'] : "";

            $stmt_detail->bind_param("iisids", $order_id, $pid, $name, $qty, $price, $note);
            $stmt_detail->execute();
        }
        $stmt_detail->close();
        
        echo "Thanh toán thành công! Mã đơn: #" . $order_id;
    } else {
        echo "Lỗi khi tạo đơn: " . $conn->error;
    }
} else {
    echo "Không có dữ liệu gửi lên!";
}

$conn->query("SET FOREIGN_KEY_CHECKS=1");
$conn->close();
?>