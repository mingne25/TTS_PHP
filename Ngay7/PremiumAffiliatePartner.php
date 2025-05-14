<?php
require_once 'AffiliatePartner.php';

/**
 * Lớp cho Cộng Tác Viên cao cấp, kế thừa từ CTV thường
 */
class PremiumAffiliatePartner extends AffiliatePartner {
    // Thuộc tính riêng của CTV cao cấp
    private $bonusPerOrder;  // Tiền thưởng cố định cho mỗi đơn hàng
    
    /**
     * Khởi tạo CTV cao cấp
     */
    public function __construct($name, $email, $commissionRate, $bonusPerOrder, $isActive = true) {
        // Gọi constructor của lớp cha để khởi tạo thông tin cơ bản
        parent::__construct($name, $email, $commissionRate, $isActive);
        $this->bonusPerOrder = $bonusPerOrder;
    }
    
    /**
     * Ghi đè phương thức tính hoa hồng để thêm tiền thưởng
     */
    public function calculateCommission($orderValue) {
        if (!$this->isActive) {
            return 0;
        }
        // Lấy hoa hồng cơ bản từ lớp cha
        $baseCommission = parent::calculateCommission($orderValue);
        // Cộng thêm tiền thưởng
        return $baseCommission + $this->bonusPerOrder;
    }
    
    /**
     * Ghi đè phương thức hiển thị thông tin để thêm thông tin về tiền thưởng
     */
    public function getSummary() {
        // Lấy thông tin cơ bản từ lớp cha
        $parentSummary = parent::getSummary();
        // Thêm thông tin về tiền thưởng
        return $parentSummary . sprintf(" - Thưởng mỗi đơn: %s VNĐ", 
            number_format($this->bonusPerOrder, 0, ',', '.'));
    }
    
    /**
     * Ghi đè phương thức hủy
     */
    public function __destruct() {
        echo "CTV cao cấp {$this->name} đã được giải phóng khỏi bộ nhớ.\n";
    }
}