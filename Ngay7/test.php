<?php
require_once 'AffiliateManager.php';

// Khởi tạo đối tượng quản lý CTV
$manager = new AffiliateManager();

// Tạo 2 CTV thường với tỷ lệ hoa hồng khác nhau
$partner1 = new AffiliatePartner(
    "Nguyễn Xuân An",     // Tên CTV
    "an@gmail.com",    // Email
    5.0                  // 5% hoa hồng
);

$partner2 = new AffiliatePartner(
    "Mã Xuân Giang",     // Tên CTV
    "giang@gmail.com",  // Email
    7.0                  // 7% hoa hồng
);

// Tạo 1 CTV cao cấp
$premiumPartner = new PremiumAffiliatePartner(
    "Lý Hồng Công",      // Tên CTV
    "cong@gmail.com", // Email
    10.0,                // 10% hoa hồng
    5000                // Thưởng thêm 5,000 VNĐ/đơn
);

// Thêm các CTV vào hệ thống
$manager->addPartner($partner1);
$manager->addPartner($partner2);
$manager->addPartner($premiumPartner);

// Giá trị đơn hàng mẫu: 2,000,000 VNĐ
$orderValue = 2000000;

// In danh sách tất cả CTV
echo "\n";
$manager->listPartners();
echo "\n";

// Tính và hiển thị hoa hồng cho từng CTV
echo "=== HOA HỒNG CHO ĐƠN HÀNG " . number_format($orderValue, 0, ',', '.') . " VNĐ ===\n";

// Tính hoa hồng cho từng CTV
$commission1 = $partner1->calculateCommission($orderValue);
echo "1. " . $partner1->getName() . ": " . number_format($commission1, 0, ',', '.') . " VNĐ\n";

$commission2 = $partner2->calculateCommission($orderValue);
echo "2. " . $partner2->getName() . ": " . number_format($commission2, 0, ',', '.') . " VNĐ\n";

$commissionPremium = $premiumPartner->calculateCommission($orderValue);
echo "3. " . $premiumPartner->getName() . " (Cao cấp): " . number_format($commissionPremium, 0, ',', '.') . " VNĐ\n";

// Tính và hiển thị tổng hoa hồng
$totalCommission = $manager->totalCommission($orderValue);
echo "\n=== TỔNG HOA HỒNG PHẢI TRẢ ===\n";
echo "Tổng cộng: " . number_format($totalCommission, 0, ',', '.') . " VNĐ\n";

// Khi kết thúc chương trình, các đối tượng sẽ tự động được hủy
// và phương thức __destruct sẽ được gọi