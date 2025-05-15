<?php
/**
 * Lớp SavingsAccount - Đại diện cho tài khoản tiết kiệm
 * Kế thừa từ BankAccount và triển khai InterestBearing để tính lãi suất.
 * Tài khoản này yêu cầu duy trì số dư tối thiểu và có khả năng sinh lời.
*/
namespace XYZBank\Accounts;

class SavingsAccount extends BankAccount implements InterestBearing {
    // Lãi suất hàng năm (5%)
    private const ANNUAL_INTEREST_RATE = 0.05;
    
    // Số dư tối thiểu phải duy trì (1 triệu VNĐ)
    private const MINIMUM_BALANCE = 1000000;

    /**
     * Thực hiện gửi tiền vào tài khoản
     * $amount Số tiền cần gửi
    */
    public function deposit(float $amount): void {
        // Cộng số tiền gửi vào số dư hiện tại
        $this->balance += $amount;
        // Ghi log giao dịch gửi tiền
        $this->logTransaction('Gửi tiền', $amount, $this->balance, $this->ownerName);
    }

    /**
     * Thực hiện rút tiền từ tài khoản
     * $amount Số tiền cần rút
     * \Exception Nếu số dư sau khi rút nhỏ hơn số dư tối thiểu
    */
    public function withdraw(float $amount): void {
        // Kiểm tra ràng buộc số dư tối thiểu
        if ($this->balance - $amount < self::MINIMUM_BALANCE) {
            throw new \Exception('Không thể rút tiền. Số dư tối thiểu phải là 1.000.000 VNĐ');
        }
        // Trừ số tiền rút khỏi số dư hiện tại
        $this->balance -= $amount;
        // Ghi log giao dịch rút tiền
        $this->logTransaction('Rút tiền', $amount, $this->balance, $this->ownerName);
    }

    
    // Tính toán lãi suất hàng năm dựa trên số dư hiện tại
    public function calculateAnnualInterest(): float {
        return $this->balance * self::ANNUAL_INTEREST_RATE;
    }

    // Lấy loại tài khoản
    public function getAccountType(): string {
        return 'Tiết kiệm';
    }
}
