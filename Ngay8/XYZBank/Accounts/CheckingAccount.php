<?php
/**
 * CheckingAccount - Đại diện cho tài khoản thanh toán
 * Kế thừa từ BankAccount, không yêu cầu số dư tối thiểu
 * và không có tính năng tính lãi suất.
*/
namespace XYZBank\Accounts;

class CheckingAccount extends BankAccount {
    /**
     * Thực hiện gửi tiền vào tài khoản thanh toán
     * $amount Số tiền cần gửi
    */
    public function deposit(float $amount): void {
        // Cộng số tiền gửi vào số dư hiện tại
        $this->balance += $amount;
        // Ghi log giao dịch gửi tiền
        $this->logTransaction('Gửi tiền', $amount, $this->balance, $this->ownerName);
    }

    /**
     * Thực hiện rút tiền từ tài khoản thanh toán
     * $amount Số tiền cần rút
     * \Exception Nếu số dư không đủ để thực hiện giao dịch
    */
    public function withdraw(float $amount): void {
        // Kiểm tra số dư có đủ để rút tiền không
        if ($amount > $this->balance) {
            throw new \Exception('Số dư không đủ để thực hiện giao dịch');
        }
        // Trừ số tiền rút khỏi số dư hiện tại
        $this->balance -= $amount;
        // Ghi log giao dịch rút tiền
        $this->logTransaction('Rút tiền', $amount, $this->balance, $this->ownerName);
    }


    // Lấy loại tài khoản
    public function getAccountType(): string {
        return 'Thanh toán';
    }
}
