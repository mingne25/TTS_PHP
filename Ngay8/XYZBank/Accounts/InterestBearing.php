<?php
/**
 * Interface InterestBearing - Giao diện cho các tài khoản có tính lãi suất
 * Interface này định nghĩa phương thức tính lãi suất hàng năm cho các loại tài khoản có khả năng sinh lời (như tài khoản tiết kiệm).
*/
namespace XYZBank\Accounts;

interface InterestBearing {
    // Tính toán lãi suất hàng năm cho tài khoản
    public function calculateAnnualInterest(): float;
}
