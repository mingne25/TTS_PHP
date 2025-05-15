<?php
require_once '../../config/db.php';
require_once '../../models/Order.php';
require_once '../../models/OrderItem.php';

// Khởi tạo session nếu chưa có
session_start();

// Kiểm tra có ID để xóa không
if (!isset($_POST['delete_id'])) {
    $_SESSION['message'] = "Không tìm thấy đơn hàng cần xóa!";
    $_SESSION['message_type'] = "danger";
    header('Location: index.php');
    exit;
}

// Khởi tạo đối tượng
$order = new Order($pdo);
$orderItem = new OrderItem($pdo);

try {
    // Bắt đầu transaction
    $pdo->beginTransaction();

    $orderId = (int)$_POST['delete_id'];

    // Xóa các chi tiết đơn hàng trước
    $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->execute([$orderId]);

    // Sau đó xóa đơn hàng
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);

    // Commit transaction
    $pdo->commit();

    $_SESSION['message'] = "Xóa đơn hàng thành công!";
    $_SESSION['message_type'] = "success";

} catch (Exception $e) {
    // Rollback nếu có lỗi
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    $_SESSION['message'] = "Có lỗi xảy ra khi xóa đơn hàng!";
    $_SESSION['message_type'] = "danger";
    error_log("Error deleting order: " . $e->getMessage());
}

// Chuyển hướng về trang danh sách
header('Location: index.php');
exit;