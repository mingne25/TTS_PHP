<?php
// Khởi động session
session_start();

// Import các file cần thiết
require_once 'functions.php';
require_once 'CartException.php';

// Danh sách sách có sẵn (tương tự như trong index.php)
$available_books = [
    ['id' => 1, 'title' => 'Clean Code', 'price' => 150000],
    ['id' => 2, 'title' => 'Design Patterns', 'price' => 200000],
    ['id' => 3, 'title' => 'Refactoring', 'price' => 180000],
    ['id' => 4, 'title' => 'Domain Driven Design', 'price' => 250000],
    ['id' => 5, 'title' => 'Pragmatic Programmer', 'price' => 170000]
];

// Khởi tạo giỏ hàng nếu chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        'products' => [],
        'total_amount' => 0
    ];
}

// Xử lý thêm sản phẩm vào giỏ hàng
if (isset($_POST['add_to_cart']) || isset($_POST['checkout'])) {
    try {
        // Xác thực và lọc dữ liệu đầu vào
        $book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_VALIDATE_REGEXP, [
            'options' => ['regexp' => '/^[0-9]{10,11}$/']
        ]);
        $address = filter_input(INPUT_POST, 'address', FILTER_CALLBACK, [
            'options' => 'sanitizeText'
        ]);

        // Kiểm tra dữ liệu đầu vào
        if (!$book_id) {
            throw new CartException("Vui lòng chọn sách.");
        }
        
        if (!$quantity || $quantity < 1) {
            throw new CartException("Số lượng không hợp lệ.");
        }
        
        if (!$email) {
            throw new CartException("Email không hợp lệ.");
        }
        
        if (!$phone) {
            throw new CartException("Số điện thoại không hợp lệ (phải có 10-11 chữ số).");
        }
        
        if (!$address) {
            throw new CartException("Địa chỉ không được để trống.");
        }

        // Tìm thông tin sách từ ID
        $selected_book = null;
        foreach ($available_books as $book) {
            if ($book['id'] == $book_id) {
                $selected_book = $book;
                break;
            }
        }

        if (!$selected_book) {
            throw new CartException("Sách không tồn tại.");
        }

        // Cập nhật thông tin khách hàng vào session
        $_SESSION['customer'] = [
            'email' => $email,
            'phone' => $phone,
            'address' => $address
        ];

        // Lưu email vào cookie
        setcookie('customer_email', $email, time() + (7 * 24 * 60 * 60), '/'); // Cookie có thời hạn 7 ngày

        // Kiểm tra xem sản phẩm đã tồn tại trong giỏ hàng chưa
        $product_exists = false;
        foreach ($_SESSION['cart']['products'] as &$product) {
            if ($product['id'] == $book_id) {
                $product['quantity'] += $quantity;
                $product_exists = true;
                break;
            }
        }
        unset($product); // Hủy tham chiếu

        // Nếu sản phẩm chưa tồn tại, thêm mới vào giỏ hàng
        if (!$product_exists) {
            $_SESSION['cart']['products'][] = [
                'id' => $book_id,
                'title' => $selected_book['title'],
                'price' => $selected_book['price'],
                'quantity' => $quantity
            ];
        }

        // Cập nhật tổng tiền
        $_SESSION['cart']['total_amount'] = calculateTotalAmount($_SESSION['cart']['products']);

        // Lưu thông tin giỏ hàng vào file JSON
        saveCartToJson($_SESSION['cart'], $_SESSION['customer']);

        // Thông báo thành công
        // $_SESSION['success'] = "Đã thêm sách vào giỏ hàng.";

        //Thống báo hoàn tất đơn hàng
        if (isset($_POST['checkout'])) {
            $_SESSION['success'] = "Đơn hàng đã được xác nhận.";
        } else {
            $_SESSION['success'] = "Đã thêm sách vào giỏ hàng.";
        }
        

        // Nếu người dùng bấm "Xác nhận đặt hàng", hiển thị thông tin thanh toán
        if (isset($_POST['checkout'])) {
            // Không chuyển hướng và hiển thị trang thanh toán
        } else {
            // Quay lại trang chủ
            header("Location: index.php");
            exit;
        }
    } catch (CartException $e) {
        $_SESSION['error'] = $e->getMessage();
        logError($e->getMessage());
        if (!isset($_POST['checkout'])) {
            header("Location: index.php");
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
        logError($e->getMessage());
        if (!isset($_POST['checkout'])) {
            header("Location: index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Thông tin đơn hàng</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
            <div class="mb-3">
                <a href="index.php" class="btn btn-primary">Quay lại trang chủ</a>
            </div>
        <?php elseif (isset($_POST['checkout']) && !empty($_SESSION['cart']['products'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Thông tin khách hàng</h3>
                </div>
                <div class="card-body">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['customer']['email']); ?></p>
                    <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($_SESSION['customer']['phone']); ?></p>
                    <p><strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($_SESSION['customer']['address']); ?></p>
                    <p><strong>Thời gian đặt hàng:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Danh sách sản phẩm</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tên sách</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['cart']['products'] as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['title']); ?></td>
                                    <td><?php echo number_format($product['price']); ?> VND</td>
                                    <td><?php echo $product['quantity']; ?></td>
                                    <td><?php echo number_format($product['price'] * $product['quantity']); ?> VND</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-primary">
                                <td colspan="3"><strong>Tổng cộng</strong></td>
                                <td><strong><?php echo number_format($_SESSION['cart']['total_amount']); ?> VND</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">Tiếp tục mua sắm</a>
                <form method="post" action="index.php">
                    <button type="submit" name="clear_cart" class="btn btn-success">Hoàn tất đơn hàng</button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                Không có sản phẩm trong giỏ hàng.
            </div>
            <div class="mb-3">
                <a href="index.php" class="btn btn-primary">Quay lại trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>