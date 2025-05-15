<?php
/**
 * Trait TransactionLogger - Cung cấp chức năng ghi log giao dịch
 * Trait này được sử dụng để ghi lại thông tin chi tiết về các giao dịch như gửi tiền, rút tiền trong các tài khoản ngân hàng.
 * Định dạng log bao gồm: thời gian, loại giao dịch, số tiền, số dư mới và người thực hiện.
 */
namespace XYZBank\Accounts;

trait TransactionLogger {
    /**
     * Ghi log một giao dịch với đầy đủ thông tin
     * $type Loại giao dịch (ví dụ: "Gửi tiền", "Rút tiền")
     * $amount Số tiền giao dịch
     * $newBalance Số dư mới sau giao dịch
     * $name Tên người thực hiện giao dịch
     */
    protected function logTransaction(string $type, float $amount, float $newBalance, string $name): void {
        // Lấy thời gian hiện tại
        $timestamp = date('Y-m-d H:i:s');
        
        // Định dạng số tiền theo format tiền Việt Nam
        $formattedAmount = number_format($amount, 0, ',', '.') . ' VNĐ';
        $formattedBalance = number_format($newBalance, 0, ',', '.') . ' VNĐ';
        
        // Xử lý bảo mật cho tên người dùng
        $formattedName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        
        // In thông tin giao dịch theo định dạng chuẩn
        echo "[{$timestamp}] Giao dịch: {$type} {$formattedAmount} | Số dư mới: {$formattedBalance} | Người thực hiện: {$formattedName}\n";
    }
}
