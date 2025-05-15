<?php
/**
 * Lớp trừu tượng BankAccount - Lớp cơ sở cho tất cả các loại tài khoản ngân hàng
 * Định nghĩa cấu trúc và hành vi cơ bản của một tài khoản ngân hàng.
 * Sử dụng TransactionLogger để ghi log các giao dịch.
*/
namespace XYZBank\Accounts;

abstract class BankAccount {
    use TransactionLogger;

    // Mã số tài khoản 
    protected string $accountNumber;

    // Tên chủ tài khoản
    protected string $ownerName;

    // Số dư tài khoản
    protected float $balance;

    /**
     * Khởi tạo một tài khoản mới
     * $accountNumber Mã số tài khoản
     * $ownerName Tên chủ tài khoản
     * $balance Số dư ban đầu
    */
    public function __construct(string $accountNumber, string $ownerName, float $balance) {
        $this->accountNumber = $accountNumber;
        $this->ownerName = $ownerName;
        $this->balance = $balance;
    }

    // Lấy số dư hiện tại của tài khoản
    public function getBalance(): float {
        return $this->balance;
    }

    // Lấy tên chủ tài khoản
    public function getOwnerName(): string {
        return $this->ownerName;
    }

    // Lấy mã số tài khoản
    public function getAccountNumber(): string {
        return $this->accountNumber;
    }

    /**
     * Thực hiện gửi tiền vào tài khoản
     * $amount Số tiền cần gửi
    */
    abstract public function deposit(float $amount): void;

    /**
     * Thực hiện rút tiền từ tài khoản
     * $amount Số tiền cần rút
    */
    abstract public function withdraw(float $amount): void;

    /**
     * Lấy loại tài khoản
     * Chuỗi mô tả loại tài khoản
    */
    abstract public function getAccountType(): string;
}
