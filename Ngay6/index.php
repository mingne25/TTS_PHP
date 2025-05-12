<?php
// Khởi động session
session_start();

// Import các file cần thiết
require_once 'functions.php';
require_once 'CartException.php';

// Khởi tạo giỏ hàng nếu chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        'products' => [],
        'total_amount' => 0
    ];
}

// Danh sách sách có sẵn (trong thực tế có thể lấy từ database)
$available_books = [
    ['id' => 1, 'title' => 'Clean Code', 'price' => 150000],
    ['id' => 2, 'title' => 'Design Patterns', 'price' => 200000],
    ['id' => 3, 'title' => 'Refactoring', 'price' => 180000],
    ['id' => 4, 'title' => 'Domain Driven Design', 'price' => 250000],
    ['id' => 5, 'title' => 'Pragmatic Programmer', 'price' => 170000]
];

// Đọc email từ cookie nếu có
$customer_email = isset($_COOKIE['customer_email']) ? $_COOKIE['customer_email'] : '';

// Kiểm tra nếu có yêu cầu xóa giỏ hàng
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [
        'products' => [],
        'total_amount' => 0
    ];
    
    // Xóa file cart_data.json nếu tồn tại
    try {
        if (file_exists('cart_data.json')) {
            if (!unlink('cart_data.json')) {
                throw new CartException("Không thể xóa file cart_data.json");
            }
        }
    } catch (CartException $e) {
        logError($e->getMessage());
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhà sách trực tuyến</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Nhà sách trực tuyến</h1>
        
        <?php
        // Hiển thị thông báo lỗi nếu có
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        
        // Hiển thị thông báo thành công nếu có
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Thêm sách vào giỏ hàng</h3>
                    </div>
                    <div class="card-body">
                        <form action="cart.php" method="post">
                            <div class="mb-3">
                                <label for="book" class="form-label">Chọn sách:</label>
                                <select name="book_id" id="book" class="form-select" >
                                    <option value="">-- Chọn sách --</option>
                                    <?php foreach ($available_books as $book): ?>
                                        <option value="<?php echo $book['id']; ?>"><?php echo htmlspecialchars($book['title']); ?> - <?php echo number_format($book['price']); ?> VND</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Số lượng:</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1" >
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($customer_email); ?>" >
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại:</label>
                                <input type="text" name="phone" id="phone" class="form-control" >
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Địa chỉ giao hàng:</label>
                                <textarea name="address" id="address" class="form-control" ></textarea>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="submit" name="add_to_cart" class="btn btn-primary">Thêm vào giỏ hàng</button>
                                <button type="submit" name="checkout" class="btn btn-success">Xác nhận đặt hàng</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3>Giỏ hàng của bạn</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($_SESSION['cart']['products'])): ?>
                            <p class="text-muted">Giỏ hàng của bạn đang trống.</p>
                        <?php else: ?>
                            <ul class="list-group mb-3">
                                <?php foreach ($_SESSION['cart']['products'] as $product): ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <div>
                                            <h6><?php echo htmlspecialchars($product['title']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo $product['quantity']; ?> x 
                                                <?php echo number_format($product['price']); ?> VND
                                            </small>
                                        </div>
                                        <span class="text-muted">
                                            <?php echo number_format($product['quantity'] * $product['price']); ?> VND
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Tổng cộng:</strong>
                                    <strong><?php echo number_format($_SESSION['cart']['total_amount']); ?> VND</strong>
                                </li>
                            </ul>
                            
                            <form method="post">
                                <button type="submit" name="clear_cart" class="btn btn-danger w-100">Xóa giỏ hàng</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>