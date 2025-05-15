<?php
require_once '../../config/db.php';
require_once '../../models/Order.php';

// Khởi tạo đối tượng Order
$order = new Order($pdo);

// Lấy danh sách đơn hàng
$orders = $order->getAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng - TechFactory</title>
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
        <h2>Quản Lý Đơn Hàng</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?>">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <?php 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
        </div>
        <?php endif; ?>

        <a href="create.php" class="btn btn-primary mb-3">Thêm Đơn Hàng</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ngày Đặt</th>
                    <th>Khách Hàng</th>
                    <th>Ghi Chú</th>
                    <th>Tổng Tiền</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="6" class="text-center">Không có đơn hàng nào</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($orders as $ord): ?>
                    <tr>
                        <td><?= htmlspecialchars($ord['id']) ?></td>
                        <td><?= date('d/m/Y', strtotime($ord['order_date'])) ?></td>
                        <td><?= htmlspecialchars($ord['customer_name']) ?></td>
                        <td><?= htmlspecialchars($ord['note']) ?></td>
                        <td><?= number_format($order->getTotalAmount($ord['id'])) ?> VNĐ</td>
                        <td>
                            <a href="detail.php?id=<?= $ord['id'] ?>" class="btn btn-sm btn-info">Chi Tiết</a>
                            <a href="edit.php?id=<?= $ord['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                            <button onclick="confirmDelete(<?= $ord['id'] ?>)" class="btn btn-sm btn-danger">Xóa</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
    function confirmDelete(id) {
        if (confirm('Bạn có chắc muốn xóa đơn hàng này?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'delete.php';
            form.innerHTML = `<input type="hidden" name="delete_id" value="${id}">`;
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>