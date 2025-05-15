<?php
require_once '../../config/db.php';
require_once '../../models/Product.php';

// Khởi tạo đối tượng Product
$product = new Product($pdo);

// Lấy ID sản phẩm từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Lấy thông tin sản phẩm
$productData = $product->getById($id);
if (!$productData) {
    header('Location: index.php');
    exit;
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['product_name']);
    $price = filter_var($_POST['unit_price'], FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST['stock_quantity'], FILTER_VALIDATE_INT);
    
    if ($name && $price !== false && $stock !== false) {
        if ($product->update($id, $name, $price, $stock)) {
            $_SESSION['message'] = 'Cập nhật sản phẩm thành công!';
            $_SESSION['message_type'] = 'success';
            header('Location: index.php');
            exit;
        }
    }
    $_SESSION['message'] = 'Dữ liệu không hợp lệ!';
    $_SESSION['message_type'] = 'danger';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Sản Phẩm - TechFactory</title>
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
        <h2>Sửa Sản Phẩm</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?>">
            <?= $_SESSION['message'] ?>
            <?php 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
        </div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($productData['id']) ?>">
            <div class="mb-3">
                <label for="product_name" class="form-label">Tên Sản Phẩm</label>
                <input type="text" 
                       class="form-control" 
                       id="product_name" 
                       name="product_name" 
                       value="<?= htmlspecialchars($productData['product_name']) ?>" 
                       required>
            </div>
            <div class="mb-3">
                <label for="unit_price" class="form-label">Giá (VNĐ)</label>
                <input type="number" 
                       step="1" 
                       class="form-control" 
                       id="unit_price" 
                       name="unit_price" 
                       value="<?= htmlspecialchars($productData['unit_price']) ?>" 
                       required>
            </div>
            <div class="mb-3">
                <label for="stock_quantity" class="form-label">Tồn Kho</label>
                <input type="number" 
                       class="form-control" 
                       id="stock_quantity" 
                       name="stock_quantity" 
                       value="<?= htmlspecialchars($productData['stock_quantity']) ?>" 
                       required>
            </div>
            <button type="submit" class="btn btn-primary">Cập Nhật</button>
            <a href="index.php" class="btn btn-secondary">Hủy</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>