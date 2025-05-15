<?php

require_once __DIR__ . '/XYZBank/Accounts/TransactionLogger.php';
require_once __DIR__ . '/XYZBank/Accounts/InterestBearing.php';
require_once __DIR__ . '/XYZBank/Accounts/BankAccount.php';
require_once __DIR__ . '/XYZBank/Accounts/SavingsAccount.php';
require_once __DIR__ . '/XYZBank/Accounts/CheckingAccount.php';
require_once __DIR__ . '/XYZBank/Accounts/Bank.php';
require_once __DIR__ . '/XYZBank/Accounts/AccountCollection.php';

use XYZBank\Accounts\{SavingsAccount, CheckingAccount, AccountCollection, Bank};

// Khởi tạo collection để quản lý tài khoản
$accounts = new AccountCollection();

// Tạo tài khoản tiết kiệm cho Nguyễn Thị A
$savingsA = new SavingsAccount('10201100', 'Nguyễn Thị A', 20000000);
$accounts->addAccount($savingsA);

// Tạo tài khoản thanh toán cho Lê Văn B
$checkingB = new CheckingAccount('20871789', 'Lê Văn B', 8000000);
$accounts->addAccount($checkingB);

// Tạo tài khoản thanh toán cho Trần Minh C
$checkingC = new CheckingAccount('36485124', 'Trần Minh C', 12000000);
$accounts->addAccount($checkingC);

// Thực hiện các giao dịch
$checkingB->deposit(5000000); // Gửi thêm 5.000.000 vào tài khoản của Lê Văn B
$checkingC->withdraw(2000000); // Rút 2.000.000 từ tài khoản của Trần Minh C

// In thông tin tất cả tài khoản
foreach ($accounts as $account) {
    echo sprintf(
        "Tài khoản: %s | %s | Loại: %s | Số dư: %s VNĐ\n",
        $account->getAccountNumber(),
        $account->getOwnerName(),
        $account->getAccountType(),
        number_format($account->getBalance(), 0, ',', '.')
    );
}
echo "\n";

// Tính và hiển thị lãi suất hàng năm của tài khoản tiết kiệm
$annualInterest = $savingsA->calculateAnnualInterest();
echo sprintf(
    "Lãi suất hàng năm cho %s: %s VNĐ\n",
    $savingsA->getOwnerName(),
    number_format($annualInterest, 0, ',', '.')
);
echo "\n";

// In tổng số tài khoản và tên ngân hàng
echo sprintf("Tổng số tài khoản đã tạo: %d\n", Bank::getTotalAccounts());
echo sprintf("Tên ngân hàng: %s\n", Bank::getBankName());
