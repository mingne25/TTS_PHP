<?php
require_once '../config/db.php';
require_once '../models/Product.php';
require_once '../models/Order.php';

// Khởi tạo đối tượng
$product = new Product($pdo);
$order = new Order($pdo);

// Xử lý các action
$action = $_GET['action'] ?? '';
$products = [];

switch($action) {
    case 'expensive':
        // Lọc sản phẩm > 1tr
        $products = $product->getByPrice(1000000);
        $title = "Sản phẩm giá trên 1.000.000 VNĐ";
        break;
    case 'by_price':
        // Sắp xếp theo giá giảm dần
        $products = $product->getByPriceDesc();
        $title = "Sản phẩm theo giá giảm dần";
        break;
    case 'latest':
        // 5 sản phẩm mới nhất
        $products = $product->getLatest(5);
        $title = "5 sản phẩm mới nhất";
        break;
    default:
        // Mặc định hiển thị tất cả
        $products = $product->getAll();
        $title = "Tất cả sản phẩm";
}

// Xử lý xóa sản phẩm
if (isset($_POST['delete_id'])) {
    if ($product->delete($_POST['delete_id'])) {
        $_SESSION['message'] = "Xóa sản phẩm thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Xóa sản phẩm thất bại!";
        $_SESSION['message_type'] = "danger";
    }
    header('Location: index.php');
    exit;
}

// Xử lý cập nhật sản phẩm
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    $name = $_POST['name']; // Assuming the missing argument is the product name
    if ($product->update($id, $name, $price, $stock)) {
        $_SESSION['message'] = "Cập nhật thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Cập nhật thất bại!";
        $_SESSION['message_type'] = "danger";
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
    <title>TechFactory - Quản Lý Sản Xuất</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">TechFactory</a>
            <div class="navbar-nav">
                <a class="nav-link" href="products/index.php">Sản Phẩm</a>
                <a class="nav-link" href="orders/index.php">Đơn Hàng</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
    <!-- Thêm filter buttons -->
    <div class="mb-3">
        <a href="index.php" class="btn btn-outline-primary">Tất cả</a>
        <a href="index.php?action=expensive" class="btn btn-outline-primary">Trên 1 triệu</a>
        <a href="index.php?action=by_price" class="btn btn-outline-primary">Giá giảm dần</a>
        <a href="index.php?action=latest" class="btn btn-outline-primary">Mới nhất</a>
    </div>

    <h2><?= htmlspecialchars($title) ?></h2>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá (VNĐ)</th>
                    <th>Tồn kho</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                <tr>
                    <td><?= htmlspecialchars($prod['id']) ?></td>
                    <td><?= htmlspecialchars($prod['product_name']) ?></td>
                    <td><?= number_format($prod['unit_price']) ?></td>
                    <td>
                        <span class="stock-display"><?= $prod['stock_quantity'] ?></span>
                        <form class="stock-form d-none" method="POST">
                            <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                            <input type="number" name="price" value="<?= $prod['unit_price'] ?>" class="form-control form-control-sm mb-1">
                            <input type="number" name="stock" value="<?= $prod['stock_quantity'] ?>" class="form-control form-control-sm mb-1">
                            <button type="submit" name="update" class="btn btn-sm btn-success">Lưu</button>
                            <button type="button" class="btn btn-sm btn-secondary cancel-edit">Hủy</button>
                        </form>
                    </td>
                    <td><?= date('d/m/Y', strtotime($prod['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>