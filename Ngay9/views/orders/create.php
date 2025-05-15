<?php
require_once '../../config/db.php';
require_once '../../models/Product.php';
require_once '../../models/Order.php';
require_once '../../models/OrderItem.php';

// Khởi tạo đối tượng
$product = new Product($pdo);
$order = new Order($pdo);
$orderItem = new OrderItem($pdo);

// Lấy danh sách sản phẩm cho select box
$products = $product->getAll();

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate dữ liệu đầu vào
        if (empty($_POST['customer_name'])) {
            throw new Exception("Vui lòng nhập tên khách hàng!");
        }

        if (empty($_POST['product_id']) || empty($_POST['quantity'])) {
            throw new Exception("Vui lòng chọn ít nhất một sản phẩm!");
        }

        // Đảm bảo không có transaction đang chạy
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        // Bắt đầu transaction
        $pdo->beginTransaction();

        // Thêm đơn hàng
        $orderId = $order->add(
            $_POST['order_date'],
            $_POST['customer_name'],
            $_POST['note'] ?? ''
        );

        if (!$orderId) {
            throw new Exception("Không thể tạo đơn hàng!");
        }

        // Xử lý từng sản phẩm
        foreach ($_POST['product_id'] as $key => $productId) {
            if (empty($productId)) continue;
            
            $quantity = (int)$_POST['quantity'][$key];
            if ($quantity <= 0) continue;

            $productInfo = $product->getById($productId);
            if (!$productInfo) {
                throw new Exception("Không tìm thấy sản phẩm!");
            }

            if (!$orderItem->checkStock($productId, $quantity)) {
                throw new Exception("Sản phẩm {$productInfo['product_name']} không đủ số lượng trong kho!");
            }

            if (!$orderItem->add($orderId, $productId, $quantity, $productInfo['unit_price'])) {
                throw new Exception("Lỗi khi thêm chi tiết đơn hàng!");
            }
        }

        // Commit transaction
        $pdo->commit();
        
        $_SESSION['message'] = "Thêm đơn hàng thành công!";
        $_SESSION['message_type'] = "success";
        header('Location: index.php');
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['message'] = "Lỗi: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Đơn Hàng - TechFactory</title>
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
        <h2>Thêm Đơn Hàng</h2>
        
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
                <label for="order_date" class="form-label">Ngày Đặt Hàng</label>
                <input type="date" class="form-control" id="order_date" name="order_date" 
                       value="<?= date('Y-m-d') ?>" >
            </div>
            <div class="mb-3">
                <label for="customer_name" class="form-label">Tên Khách Hàng</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" >
            </div>
            <div class="mb-3">
                <label for="note" class="form-label">Ghi Chú</label>
                <textarea class="form-control" id="note" name="note" rows="4"></textarea>
            </div>
            <h4>Thêm Sản Phẩm</h4>
            <div id="order-items">
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label for="product_id" class="form-label">Sản Phẩm</label>
                        <select class="form-select product-select" name="product_id[]" >
                            <option value="">Chọn sản phẩm</option>
                            <?php foreach ($products as $prod): ?>
                            <option value="<?= $prod['id'] ?>" data-price="<?= $prod['unit_price'] ?>">
                                <?= htmlspecialchars($prod['product_name']) ?> 
                                (<?= number_format($prod['unit_price']) ?> VNĐ - Còn: <?= $prod['stock_quantity'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="quantity" class="form-label">Số Lượng</label>
                        <input type="number" class="form-control quantity-input" name="quantity[]" min="1" >
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger mt-4" onclick="removeItem(this)">Xóa</button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary mb-3" onclick="addItem()">Thêm Sản Phẩm</button>
            <br>
            <button type="submit" class="btn btn-primary">Lưu Đơn Hàng</button>
            <a href="index.php" class="btn btn-secondary">Hủy</a>
        </form>
    </div>

    <script>
        function removeItem(button) {
            const itemsContainer = document.getElementById('order-items');
            const items = itemsContainer.querySelectorAll('.row');
            
            // Chỉ xóa nếu có nhiều hơn 1 sản phẩm
            if (items.length > 1) {
                button.closest('.row').remove();
            } else {
                alert('Đơn hàng phải có ít nhất một sản phẩm!');
            }
        }

        // Thêm sản phẩm mới
        function addItem() {
            const itemsContainer = document.getElementById('order-items');
            const template = itemsContainer.querySelector('.row').cloneNode(true);
            
            // Reset giá trị
            template.querySelector('select').value = '';
            template.querySelector('input[type="number"]').value = '';
            
            itemsContainer.appendChild(template);
        }

        // Thêm sự kiện cho nút "Thêm Sản Phẩm"
        document.querySelector('.btn-secondary').onclick = addItem;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>