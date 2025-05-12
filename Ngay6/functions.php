<?php
/**
 * Tính tổng tiền từ danh sách sản phẩm trong giỏ hàng
 * 
 * @param array $products Danh sách sản phẩm
 * @return float Tổng tiền
 */
function calculateTotalAmount($products) {
    $total = 0;
    foreach ($products as $product) {
        $total += $product['price'] * $product['quantity'];
    }
    return $total;
}

/**
 * Lọc và làm sạch văn bản đầu vào
 * 
 * @param string $text Văn bản cần làm sạch
 * @return string Văn bản đã được làm sạch
 */
function sanitizeText($text) {
    // Loại bỏ các thẻ HTML
    $text = strip_tags($text);
    // Loại bỏ khoảng trắng thừa
    $text = trim($text);
    return $text;
}

/**
 * Lưu thông tin giỏ hàng vào file JSON
 * 
 * @param array $cart Thông tin giỏ hàng
 * @param array $customer Thông tin khách hàng
 * @return bool Kết quả lưu file
 * @throws CartException Nếu không thể lưu file
 */
function saveCartToJson($cart, $customer) {
    try {
        // Chuẩn bị dữ liệu để lưu
        $data = [
            'customer_email' => $customer['email'],
            'products' => [],
            'total_amount' => $cart['total_amount'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Chuyển đổi dữ liệu sản phẩm
        foreach ($cart['products'] as $product) {
            $data['products'][] = [
                'title' => $product['title'],
                'quantity' => $product['quantity'],
                'price' => $product['price']
            ];
        }
        
        // Chuyển đổi dữ liệu thành JSON
        $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        if ($json_data === false) {
            throw new CartException("Không thể chuyển đổi dữ liệu sang JSON: " . json_last_error_msg());
        }
        
        // Ghi file
        $result = file_put_contents('cart_data.json', $json_data);
        
        if ($result === false) {
            throw new CartException("Không thể ghi file cart_data.json");
        }
        
        return true;
    } catch (Exception $e) {
        throw new CartException("Lỗi khi lưu giỏ hàng: " . $e->getMessage());
    }
}

/**
 * Ghi log lỗi vào file
 * 
 * @param string $message Thông báo lỗi
 * @return void
 */
function logError($message) {
    $log_message = "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL;
    file_put_contents('log.txt', $log_message, FILE_APPEND);
}