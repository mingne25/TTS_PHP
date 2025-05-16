<?php
session_start();
header('Content-Type: application/json');

// Nhận dữ liệu từ request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['productId'])) {
    // Khởi tạo giỏ hàng nếu chưa có
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Thêm sản phẩm vào giỏ hàng
    $_SESSION['cart'][] = $data['productId'];
    
    echo json_encode([
        'success' => true,
        'cartCount' => count($_SESSION['cart']),
        'message' => 'Thêm vào giỏ hàng thành công'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin sản phẩm'
    ]);
}