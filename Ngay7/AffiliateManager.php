<?php
require_once 'AffiliatePartner.php';
require_once 'PremiumAffiliatePartner.php';

/**
 * Lớp Quản lý Cộng Tác Viên
 * Quản lý danh sách và tính toán hoa hồng cho các CTV
 */
class AffiliateManager {
    // Mảng lưu danh sách các CTV
    private $partners = [];
    
    /**
     * Thêm một CTV vào hệ thống
     */
    public function addPartner($affiliate) {
        $this->partners[] = $affiliate;
    }
    
    /**
     * Hiển thị danh sách tất cả CTV
     */
    public function listPartners() {
        if (empty($this->partners)) {
            echo "Chưa có CTV nào trong hệ thống.\n";
            return;
        }
        
        echo "=== DANH SÁCH CỘNG TÁC VIÊN ===\n";
        foreach ($this->partners as $partner) {
            echo $partner->getSummary() . "\n";
        }
    }
    
    /**
     * Tính tổng hoa hồng phải trả cho tất cả CTV
     */
    public function totalCommission($orderValue) {
        $total = 0;
        foreach ($this->partners as $partner) {
            $commission = $partner->calculateCommission($orderValue);
            $total += $commission;
        }
        return $total;
    }
    
    /**
     * Đếm số lượng CTV trong hệ thống
     */
    public function getPartnerCount() {
        return count($this->partners);
    }
}