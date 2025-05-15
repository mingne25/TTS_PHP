<?php
require_once '../../config/db.php';
require_once '../../models/Product.php';

// Initialize Product object
$product = new Product($pdo);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['product_name'];
    $price = $_POST['unit_price'];
    $stock = $_POST['stock_quantity'];
    
    try {
        if ($product->add($name, $price, $stock)) {
            $_SESSION['message'] = 'Thêm sản phẩm thành công!';
            $_SESSION['message_type'] = 'success';
            header('Location: index.php');
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm - TechFactory</title>
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
        <h2>Thêm Sản Phẩm</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?>">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <?php 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
        </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="product_name" class="form-label">Tên Sản Phẩm</label>
                <input type="text" 
                       class="form-control" 
                       id="product_name" 
                       name="product_name" 
                       value="<?= htmlspecialchars($_POST['product_name'] ?? '') ?>"
                       required>
            </div>
            <div class="mb-3">
                <label for="unit_price" class="form-label">Giá (VNĐ)</label>
                <input type="number" 
                       step="1000" 
                       class="form-control" 
                       id="unit_price" 
                       name="unit_price" 
                       value="<?= htmlspecialchars($_POST['unit_price'] ?? '') ?>"
                       required>
            </div>
            <div class="mb-3">
                <label for="stock_quantity" class="form-label">Tồn Kho</label>
                <input type="number" 
                       class="form-control" 
                       id="stock_quantity" 
                       name="stock_quantity" 
                       value="<?= htmlspecialchars($_POST['stock_quantity'] ?? '') ?>"
                       required>
            </div>
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="index.php" class="btn btn-secondary">Hủy</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>