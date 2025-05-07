<?php
// filepath: x:\laragon\www\THUC_TAP\PHP\buoi2\bai2.php

echo "HỆ THỐNG TÍNH HOA HỒNG AFFILIATE ĐA CẤP <br>";

// Danh sách người dùng
$users = [
    1 => ['name' => 'Alice', 'referrer_id' => null], // Không có người giới thiệu
    2 => ['name' => 'Bob', 'referrer_id' => 1], // Người giới thiệu là Alice
    3 => ['name' => 'Charlie', 'referrer_id' => 2], // Người giới thiệu là Bob
    4 => ['name' => 'David', 'referrer_id' => 3], // Người giới thiệu là Charlie
    5 => ['name' => 'Eva', 'referrer_id' => 1], // Người giới thiệu là Alice
    6 => ['name' => 'Frank', 'referrer_id' => 1], // Người giới thiệu là Alice
];

// Danh sách đơn hàng
$orders = [
    ['order_id' => 101, 'user_id' => 4, 'amount' => 200.0], // Đơn hàng của David
    ['order_id' => 102, 'user_id' => 3, 'amount' => 150.0], // Đơn hàng của Charlie
    ['order_id' => 103, 'user_id' => 5, 'amount' => 300.0], // Đơn hàng của Eva
    ['order_id' => 104, 'user_id' => 6, 'amount' => 100.0], // Đơn hàng của Frank
];

// Tỷ lệ hoa hồng theo cấp
$commissionRates = [
    1 => 0.10, // Cấp 1: 10%
    2 => 0.05, // Cấp 2: 5%
    3 => 0.02, // Cấp 3: 2%
];

// Hàm tính toán hoa hồng
function calculateCommission(array $orders, array $users, array $commissionRates): array { 
    $commissions = []; // Mảng lưu trữ hoa hồng
    foreach ($orders as $order) {
        $buyerId = $order['user_id']; // ID người mua
        $amount = $order['amount'];   // Số tiền đơn hàng
        $referrerId = $users[$buyerId]['referrer_id'] ?? null; // ID người giới thiệu
        $level = 1; // Khởi tạo cấp độ hoa hồng

        // Tính hoa hồng cho từng cấp
        while ($referrerId !== null && $level <= 3) { // Giới hạn cấp độ tối đa là 3
            $commissionAmount = $amount * ($commissionRates[$level] ?? 0); // Tính hoa hồng theo cấp
            $commissions[] = [ 
                'referrer_id' => $referrerId, // ID người giới thiệu
                'order_id' => $order['order_id'], // ID đơn hàng
                'buyer_id' => $buyerId, // ID người mua
                'level' => $level, // Cấp độ hoa hồng
                'commission' => $commissionAmount, // Số tiền hoa hồng
            ];
            $referrerId = $users[$referrerId]['referrer_id'] ?? null; // Lấy ID người giới thiệu tiếp theo
            $level++; // Tăng cấp độ hoa hồng
        }
    }
    return $commissions; // Trả về danh sách hoa hồng
}

// Tính toán hoa hồng
$commissions = calculateCommission($orders, $users, $commissionRates); // Gọi hàm tính toán hoa hồng

// Nhóm chi tiết hoa hồng theo người giới thiệu
$groupedCommissions = []; // Mảng lưu trữ hoa hồng theo người giới thiệu
foreach ($commissions as $commission) { // Duyệt qua từng hoa hồng
    $referrerId = $commission['referrer_id']; // Lấy ID người giới thiệu
    if (!isset($groupedCommissions[$referrerId])) { // Nếu chưa có người giới thiệu trong mảng
        $groupedCommissions[$referrerId] = []; // Khởi tạo mảng cho người giới thiệu
    }
    $groupedCommissions[$referrerId][] = $commission; // Thêm hoa hồng vào mảng của người giới thiệu
}


echo " 📝 Thông tin hoa hồng theo người giới thiệu: <br>";
echo "--------------------------------------------------------- <br>";
foreach ($groupedCommissions as $referrerId => $commissions) { // Duyệt qua từng người giới thiệu
    $referrerName = $users[$referrerId]['name']; // Lấy tên người giới thiệu
    $totalCommission = 0; // Khởi tạo tổng hoa hồng cho người giới thiệu

    echo " 👨‍💼 Người giới thiệu: $referrerName<br>";
    echo "============================================= <br>";
    foreach ($commissions as $commission) { // Duyệt qua từng hoa hồng của người giới thiệu
        $buyerName = $users[$commission['buyer_id']]['name']; // Lấy tên người mua
        $totalCommission += $commission['commission']; // Cộng dồn hoa hồng
        echo "↪ Đơn hàng: {$commission['order_id']}<br>"; 
        echo "   Người mua: $buyerName<br>";
        echo "   Cấp: {$commission['level']}<br>";
        echo "   Hoa hồng: $" .$commission['commission'] ."<br>";
        echo "———————————————— <br>";
        
    }
    echo "🎁 Tổng hoa hồng mà ". $referrerName ." nhận được là : $" . $totalCommission . "<br>";
    echo "----------------------------------------------------- <br>";
}