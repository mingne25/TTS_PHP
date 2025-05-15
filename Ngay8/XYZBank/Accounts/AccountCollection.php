<?php

// AccountCollection - Quản lý tập hợp các tài khoản ngân hàng  
// Lớp này triển khai giao diện IteratorAggregate để cho phép duyệt qua danh sách tài khoản
// Bằng cách sử dụng vòng lặp foreach

namespace XYZBank\Accounts;

class AccountCollection implements \IteratorAggregate {
    // Mảng lưu trữ danh sách các tài khoản 
    private array $accounts = [];
    
 
    // Thêm một tài khoản mới vào collection
    // $account Tài khoản cần thêm vào
    public function addAccount(BankAccount $account): void {
        // Thêm tài khoản vào mảng
        $this->accounts[] = $account;
        // Tăng tổng số tài khoản trong hệ thống
        Bank::incrementTotalAccounts();
    }
    
    
    // Triển khai phương thức của IteratorAggregate
    // Cho phép duyệt qua các tài khoản bằng foreach
    public function getIterator(): \ArrayIterator {
        // Trả về một ArrayIterator để duyệt qua danh sách tài khoản
        // ArrayIterator là một lớp tích hợp trong PHP cho phép duyệt qua mảng
        return new \ArrayIterator($this->accounts);
    }
    
    
    // Lọc và trả về danh sách các tài khoản có số dư lớn hơn hoặc bằng giá trị cho trước 
    // $minBalance Số dư tối thiểu (mặc định là 10,000,000 VNĐ)
    // Mảng các tài khoản thỏa mãn điều kiện
    public function getHighBalanceAccounts(float $minBalance = 10000000): array {
        // Sử dụng array_filter để lọc các tài khoản có số dư >= minBalance
        return array_filter($this->accounts, function($account) use ($minBalance) {
            return $account->getBalance() >= $minBalance;
        });
    }
}
