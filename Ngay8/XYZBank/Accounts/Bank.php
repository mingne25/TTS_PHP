<?php

namespace XYZBank\Accounts;

class Bank {
    // Số lượng tài khoản ngân hàng
    // Biến static để lưu trữ tổng số tài khoản
    private static int $totalAccounts = 0;
    

    public static function incrementTotalAccounts(): void {
        // Tăng tổng số tài khoản lên 1
        // Phương thức này được gọi khi một tài khoản mới được tạo
        self::$totalAccounts++;
    }
    
    public static function getTotalAccounts(): int {
        // Trả về tổng số tài khoản hiện có
        // Phương thức này có thể được gọi từ bất kỳ đâu
        return self::$totalAccounts;
    }
    
    public static function getBankName(): string {
        // Trả về tên ngân hàng
        // Tên ngân hàng có thể được thay đổi tùy theo yêu cầu
        return "Ngân hàng XYZ";
    }
}
