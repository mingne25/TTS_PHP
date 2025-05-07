<?php
session_start();

// Khởi tạo tổng thu, chi nếu chưa có (sử dụng biến toàn cục - $GLOBALS)
if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
    $GLOBALS['total_income'] = 0;
    $GLOBALS['total_expense'] = 0;
} else {
    // Khởi động lại tổng thu, chi mỗi lần load
    $GLOBALS['total_income'] = 0;
    $GLOBALS['total_expense'] = 0;
}

$errors = [];
$warning = '';


// Xử lý thêm giao dịch
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận dữ liệu từ form - sử dụng $_POST
    $name = trim($_POST['transaction_name']);
    $amount = trim($_POST['amount']);
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $note = trim($_POST['note']);
    $date = trim($_POST['date']);

    // Regex kiểm tra tên không chứa ký tự đặc biệt
    if (!preg_match("/^[a-zA-Z0-9\s]+$/u", $name)) {
        $errors[] = "Tên giao dịch không được chứa ký tự đặc biệt.";
    }

    // Regex kiểm tra số tiền là số dương
    if (!preg_match("/^\d+(\.\d{1,2})?$/", $amount)) {
        $errors[] = "Số tiền phải là số dương và hợp lệ.";
    }

    // Regex kiểm tra định dạng ngày dd/mm/yyyy
    if (!preg_match("/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/\d{4}$/", $date)) {
        $errors[] = "Ngày phải theo định dạng dd/mm/yyyy.";
    }

    // Cảnh báo từ khóa nhạy cảm trong ghi chú
    $blacklist = ['nợ xấu', 'vay nóng'];
    foreach ($blacklist as $badword) {
        if (stripos($note, $badword) !== false) {
            $warning = "⚠️ Ghi chú chứa từ khóa nhạy cảm: \"$badword\".";
            break;
        }
    }

    // Kiểm tra thiếu thông tin
    if (empty($name) || empty($amount) || empty($type) || empty($date)) {
        $errors[] = "Vui lòng nhập đầy đủ thông tin bắt buộc.";
    }

    // Nếu không có lỗi thì lưu vào $_SESSION
    if (empty($errors)) {
        $transaction = [
            'name' => $name,
            'amount' => (float)$amount,
            'type' => $type,
            'note' => $note,
            'date' => $date
        ];
        $_SESSION['transactions'][] = $transaction;

        // 👉 Ghi cookie tên giao dịch, tồn tại 1 giờ
        setcookie("last_transaction_name", $name, time() + 3600);

        // Sau khi lưu xong, chuyển hướng để ngăn double-submit khi refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();

    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quản lý giao dịch tài chính</title>
</head>
<body>
<h2>Nhập giao dịch tài chính</h2>
<?php
if (isset($_COOKIE['last_transaction_name'])) {
    echo "<p>Giao dịch gần nhất bạn đã nhập là: <strong>" . htmlspecialchars($_COOKIE['last_transaction_name']) . "</strong></p>";
}
?>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <label>Tên giao dịch: <input type="text" name="transaction_name"></label><br><br>
    <label>Số tiền: <input type="text" name="amount"></label><br><br>
    <label>Loại giao dịch:</label>
    <label><input type="radio" name="type" value="thu"> Thu</label>
    <label><input type="radio" name="type" value="chi"> Chi</label><br><br>
    <label>Ghi chú: <textarea name="note"></textarea></label><br><br>
    <label>Ngày thực hiện (dd/mm/yyyy): <input type="text" name="date"></label><br><br>
    <button type="submit">Gửi giao dịch</button>
</form>



<!-- Hiển thị lỗi -->
<?php
if (!empty($errors)) {
    echo "<ul style='color:red;'>";
    foreach ($errors as $error) echo "<li>$error</li>";
    echo "</ul>";
}

if ($warning) {
    echo "<p style='color:orange;'>$warning</p>";
}

?>

<!-- Hiển thị giao dịch -->
<?php
if (!empty($_SESSION['transactions'])) {
    echo "<h2>Danh sách giao dịch</h2>";
    echo "<table border='1' cellpadding='8'>";
    echo "<tr>
            <th>Tên</th>
            <th>Số tiền</th>
            <th>Loại</th>
            <th>Ghi chú</th>
            <th>Ngày</th>
        </tr>";

    foreach ($_SESSION['transactions'] as $index => $trans) {
        echo "<tr>";
        echo "<td>{$trans['name']}</td>";
        echo "<td>{$trans['amount']}</td>";
        echo "<td>{$trans['type']}</td>";
        echo "<td>{$trans['note']}</td>";
        echo "<td>{$trans['date']}</td>";

        // Cập nhật tổng thu, chi (sử dụng biến toàn cục $GLOBALS)
        if ($trans['type'] == 'thu') {
            $GLOBALS['total_income'] += $trans['amount'];
        } else {
            $GLOBALS['total_expense'] += $trans['amount'];
        }
    }

    $balance = $GLOBALS['total_income'] - $GLOBALS['total_expense'];

    echo "</table><br>";
    echo "<h3>Thống kê:</h3>";
    echo "Tổng thu: " . $GLOBALS['total_income'] . "<br>";
    echo "Tổng chi: " . $GLOBALS['total_expense'] . "<br>";
    echo "Số dư: <strong>" .$balance . "</strong><br>";
}

?>
</body>
</html>