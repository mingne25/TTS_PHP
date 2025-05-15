<?php
require_once '../../config/db.php';
require_once '../../models/Product.php';

// Khởi tạo đối tượng Product
$product = new Product($pdo);

// Lấy danh sách sản phẩm
$products = $product->getAll();

// Xử lý xóa sản phẩm nếu có
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    
    // Kiểm tra sản phẩm có trong đơn hàng không
    if ($product->isInOrder($id)) {
        $_SESSION['message'] = "Không thể xóa sản phẩm đã có trong đơn hàng!";
        $_SESSION['message_type'] = "danger";
    } else {
        if ($product->delete($id)) {
            $_SESSION['message'] = "Xóa sản phẩm thành công!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Có lỗi xảy ra khi xóa sản phẩm!";
            $_SESSION['message_type'] = "danger";
        }
    }
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm - TechFactory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">TechFactory</a>
            <div class="navbar-nav">
                <a class="nav-link active" href="index.php">Sản Phẩm</a>
                <a class="nav-link" href="../orders/index.php">Đơn Hàng</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Quản Lý Sản Phẩm</h2>
        
        <!-- Thông báo -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                <?= $_SESSION['message'] ?>
                <?php 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <a href="create.php" class="btn btn-primary mb-3">Thêm Sản Phẩm</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Giá (VNĐ)</th>
                    <th>Tồn Kho</th>
                    <th>Ngày Tạo</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?> <!-- Đổi tên biến để tránh nhầm lẫn -->
                <tr>
                    <td><?= htmlspecialchars($prod['id']) ?></td>
                    <td><?= htmlspecialchars($prod['product_name']) ?></td>
                    <td><?= number_format($prod['unit_price']) ?></td>
                    <td><?= htmlspecialchars($prod['stock_quantity']) ?></td>
                    <td><?= date('d/m/Y', strtotime($prod['created_at'])) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                        <?php if (!$product->isInOrder($prod['id'])): ?> <!-- Sử dụng biến $product từ đối tượng đã khởi tạo -->
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $prod['id'] ?>)">Xóa</button>
                        <?php else: ?>
                            <button class="btn btn-sm btn-danger" disabled title="Sản phẩm đã có trong đơn hàng">Xóa</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function confirmDelete(id) {
        if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `<input type="hidden" name="delete_id" value="${id}">`;
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>