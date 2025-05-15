<?php
require_once '../../config/db.php';
require_once '../../models/Order.php';
require_once '../../models/OrderItem.php';
require_once '../../models/Product.php';

// Khởi tạo các đối tượng
$order = new Order($pdo);
$orderItem = new OrderItem($pdo);
$product = new Product($pdo);

// Lấy ID đơn hàng từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Lấy thông tin đơn hàng
$orderInfo = $order->getById($id);
if (!$orderInfo) {
    header('Location: index.php');
    exit;
}

// Lấy chi tiết đơn hàng
$orderItems = $orderItem->getByOrder($id);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng - TechFactory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">TechFactory</a>
            <div class="navbar-nav">
                <a class="nav-link" href="../products/index.php">Sản Phẩm</a>
                <a class="nav-link active" href="index.php">Đơn Hàng</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Chi Tiết Đơn Hàng #<?= htmlspecialchars($orderInfo['id']) ?></h2>
        
        <!-- Thông tin đơn hàng -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Thông tin chung</h5>
                <p><strong>Ngày đặt:</strong> <?= date('d/m/Y', strtotime($orderInfo['order_date'])) ?></p>
                <p><strong>Khách hàng:</strong> <?= htmlspecialchars($orderInfo['customer_name']) ?></p>
                <p><strong>Ghi chú:</strong> <?= htmlspecialchars($orderInfo['note']) ?></p>
            </div>
        </div>

        <!-- Chi tiết sản phẩm -->
        <h4>Danh sách sản phẩm</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price_at_order_time']) ?> VNĐ</td>
                    <td><?= number_format($item['quantity'] * $item['price_at_order_time']) ?> VNĐ</td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                    <td><strong><?= number_format($order->getTotalAmount($id)) ?> VNĐ</strong></td>
                </tr>
            </tbody>
        </table>

        <a href="index.php" class="btn btn-secondary">Quay lại</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>