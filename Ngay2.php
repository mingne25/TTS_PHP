<?php

// Dữ liệu đầu vào
$users = [
    1 => ['name' => 'Alice', 'referrer_id' => null],
    2 => ['name' => 'Bob', 'referrer_id' => 1],
    3 => ['name' => 'Charlie', 'referrer_id' => 2],
    4 => ['name' => 'David', 'referrer_id' => 3],
    5 => ['name' => 'Eva', 'referrer_id' => 1],
];

$orders = [
    ['order_id' => 101, 'user_id' => 4, 'amount' => 200.0],
    ['order_id' => 102, 'user_id' => 3, 'amount' => 150.0],
    ['order_id' => 103, 'user_id' => 5, 'amount' => 300.0],
    ['order_id' => 104, 'user_id' => 2, 'amount' => 100.0],
];

$commissionRates = [
    1 => 0.10,
    2 => 0.05,
    3 => 0.02,
];

// Hàm lấy chuỗi người giới thiệu cho một người dùng
// Hàm này sẽ lấy chuỗi người giới thiệu cho một người dùng cụ thể, tối đa là 3 cấp độ
function getReferrerChain(int $userId, array $users, int $maxLevel = 3): array {
    $chain = [];            // Mảng lưu trữ chuỗi người giới thiệu
    $currentId = $userId;   // ID người dùng hiện tại
    $level = 1;             // Cấp độ bắt đầu

    // Lặp qua chuỗi người giới thiệu cho đến khi đạt cấp độ tối đa hoặc không còn người giới thiệu
    while ($level <= $maxLevel && isset($users[$currentId]['referrer_id'])) {
        $referrerId = $users[$currentId]['referrer_id'];    // Lấy ID người giới thiệu
        if ($referrerId === null) break;                    // Nếu không có người giới thiệu thì dừng lại

        $chain[$level] = $referrerId;           // Lưu ID người giới thiệu vào mảng chuỗi
        $currentId = $referrerId;               // Cập nhật ID người dùng hiện tại thành người giới thiệu
        $level++;                               // Tăng cấp độ lên 1
    }

    return $chain;           // Trả về mảng chuỗi người giới thiệu
}

/* 
Tính toán hoa hồng cho từng người dùng
Hàm này sẽ tính toán hoa hồng cho từng người dùng dựa trên đơn hàng và chuỗi người giới thiệu
Hàm này sẽ trả về một mảng chứa tổng hoa hồng và chi tiết hoa hồng cho từng người dùng 
*/

function calculateCommission(array $orders, array $users, array $commissionRates): array {
    $commissions = [];  // [user_id => ['total' => ..., 'details' => [...]]]

    foreach ($orders as $order) {
        $buyerId = $order['user_id'];  // ID của người mua
        $amount = $order['amount'];    // Số tiền của đơn hàng
        $orderId = $order['order_id']; // ID của đơn hàng

        $refChain = getReferrerChain($buyerId, $users); // Lấy chuỗi người giới thiệu cho người mua

        foreach ($refChain as $level => $referrerId) {
            $rate = $commissionRates[$level] ?? 0.0;    // Tỷ lệ hoa hồng cho cấp độ này
            $commissionAmount = $amount * $rate;        // Tính toán hoa hồng
            
            if (!isset($commissions[$referrerId])) {
                $commissions[$referrerId] = [
                    'total' => 0,       // Tổng hoa hồng
                    'details' => [],    // Chi tiết hoa hồng
                ];
            }

            $commissions[$referrerId]['total'] += $commissionAmount; // Cộng dồn hoa hồng

            $commissions[$referrerId]['details'][] = [
                'from_order' => $orderId,                   // ID đơn hàng
                'buyer' => $users[$buyerId]['name'],        // Tên người mua
                'level' => $level,                          // Cấp độ hoa hồng
                'amount' => $commissionAmount,              // Số tiền hoa hồng
            ];
        }

        $maxLevel = max(array_keys($commissionRates));                  // Lấy cấp độ cao nhất từ tỷ lệ hoa hồng
        $refChain = getReferrerChain($buyerId, $users, $maxLevel);      // Reset chuỗi người giới thiệu cho người tiếp theo
    }

    return $commissions; // Trả về danh sách hoa hồng
}

/*
Tạo báo cáo hoa hồng
Hàm này sẽ in báo cáo hoa hồng cho từng người dùng
Hàm này sẽ nhận vào mảng hoa hồng và mảng người dùng, sau đó in ra báo cáo
*/
function printCommissionReport(array $commissions, array $users): void {
    // In báo cáo hoa hồng cho từng người dùng
    foreach ($commissions as $userId => $data) {
        echo "💼 " . $users[$userId]['name'] . " nhận được tổng hoa hồng: " . number_format($data['total'], 2) . " $" . "<br>" ; 
        //
        foreach ($data['details'] as $detail) {
            echo "   → Đơn: {$detail['from_order']} | Người mua: {$detail['buyer']} | Cấp: {$detail['level']} | Số tiền: " . number_format($detail['amount'], 2) . " $" . "<br>";
        }

        echo "\n";
    }
}

$commissions = calculateCommission($orders, $users, $commissionRates);  // Tính toán hoa hồng
printCommissionReport($commissions, $users);                            // In báo cáo hoa hồng




?>