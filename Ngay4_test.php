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

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Giao Dịch Tài Chính</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
        }
        .animate-pulse:hover {
            animation: pulse 0.3s ease-in-out;
        }
        .bg-gradient {
            background: linear-gradient(135deg, #6B7280, #10B981);
        }
        .shadow-glow {
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
        }
        .table-row:hover {
            background-color: #e6fff5;
            transition: background-color 0.3s ease;
        }
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 24px rgba(16, 185, 129, 0.4);
        }
        .input-focus:focus {
            border-color: #10B981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }
        .table-container {
            overflow-x: auto;
        }
        @media (max-width: 640px) {
            .table-container {
                overflow-x: auto;
            }
            .card {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen bg-gradient">
        <div class="container mx-auto px-4 py-12">
            <!-- Header -->
            <div class="text-center mb-12 animate-fadeIn">
                <h1 class="text-5xl font-extrabold text-white mb-4">Quản Lý Giao Dịch Tài Chính</h1>
                <p class="text-lg text-white opacity-80">Theo dõi thu chi dễ dàng, minh bạch và hiện đại</p>
                <?php 
                if (isset($_COOKIE['last_transaction_name'])) {
                    echo "<p class='text-xl text-white mt-2'>Giao dịch gần nhất bạn đã nhập là: <strong>" . htmlspecialchars($_COOKIE['last_transaction_name']) . "</strong></p>";
                }
                ?>
            </div>

            <!-- Form -->
            <div class="card bg-white rounded-xl p-8 shadow-glow mb-12 animate-fadeIn" style="animation-delay: 0.2s" x-data="{ type: '' }">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Nhập Giao Dịch Mới</h2>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Tên Giao Dịch</label>
                            <input type="text" name="transaction_name" class="w-full p-3 border rounded-lg input-focus" placeholder="Ví dụ: Mua sắm">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Số Tiền</label>
                            <input type="text" name="amount" class="w-full p-3 border rounded-lg input-focus" placeholder="Ví dụ: 1000">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Loại Giao Dịch</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="thu" x-model="type" class="mr-2">
                                    <span class="text-gray-700">Thu</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="chi" x-model="type" class="mr-2">
                                    <span class="text-gray-700">Chi</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Ngày (dd/mm/yyyy)</label>
                            <input type="text" name="date" class="w-full p-3 border rounded-lg input-focus" placeholder="Ví dụ: 06/05/2025">
                        </div>
                    </div>
                    <div class="mt-6">
                        <label class="block text-gray-700 font-medium mb-2">Ghi Chú</label>
                        <textarea name="note" class="w-full p-3 border rounded-lg input-focus" rows="4" placeholder="Thông tin bổ sung"></textarea>
                    </div>
                    <button type="submit" class="mt-6 bg-green-500 text-white font-bold py-3 px-6 rounded-lg animate-pulse hover:bg-green-600 transition">Gửi Giao Dịch</button>
                </form>

                <!-- Errors and Warnings -->
                <?php if (!empty($errors)) { ?>
                    <div class="mt-6 p-4 bg-red-100 text-red-700 rounded-lg">
                        <ul class="list-disc pl-5">
                            <?php foreach ($errors as $error) echo "<li>$error</li>"; ?>
                        </ul>
                    </div>
                <?php } ?>
                <?php if ($warning) { ?>
                    <div class="mt-6 p-4 bg-yellow-100 text-yellow-700 rounded-lg">
                        <?php echo $warning; ?>
                    </div>
                <?php } ?>
            </div>

            <!-- Transactions Table -->
            <?php if (!empty($_SESSION['transactions'])) { ?>
                <div class="card bg-white rounded-xl p-8 shadow-glow animate-fadeIn" style="animation-delay: 0.4s">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Danh Sách Giao Dịch</h2>
                    <div class="table-container">
                        <table class="w-full text-left">
                            <thead class="bg-green-600 text-white">
                                <tr>
                                    <th class="p-4">Tên</th>
                                    <th class="p-4">Số Tiền</th>
                                    <th class="p-4">Loại</th>
                                    <th class="p-4">Ghi Chú</th>
                                    <th class="p-4">Ngày</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($_SESSION['transactions'] as $trans) {
                                    echo "<tr class='table-row border-b border-gray-200'>";
                                    echo "<td class='p-4'>{$trans['name']}</td>";
                                    echo "<td class='p-4'>" . number_format($trans['amount']) . "</td>";
                                    echo "<td class='p-4'>" . ($trans['type'] == 'thu' ? '<span class="text-green-600">Thu</span>' : '<span class="text-red-600">Chi</span>') . "</td>";
                                    echo "<td class='p-4'>{$trans['note']}</td>";
                                    echo "<td class='p-4'>{$trans['date']}</td>";
                                    echo "</tr>";

                                    if ($trans['type'] == 'thu') {
                                        $GLOBALS['total_income'] += $trans['amount'];
                                    } else {
                                        $GLOBALS['total_expense'] += $trans['amount'];
                                    }
                                }
                                $balance = $GLOBALS['total_income'] - $GLOBALS['total_expense'];
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="card bg-green-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-700">Tổng Thu</h3>
                            <p class="text-2xl font-bold text-green-600"><?php echo number_format($GLOBALS['total_income']); ?> VND</p>
                        </div>
                        <div class="card bg-red-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-700">Tổng Chi</h3>
                            <p class="text-2xl font-bold text-red-600">
                                <?php echo number_format($GLOBALS['total_expense']); ?> VND
                            </p>
                        </div>
                        <div class="card bg-blue-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-700">Số dư</h3>
                            <p class="text-2xl font-bold text-gray-800"><?php echo number_format($balance); ?> VND</p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>