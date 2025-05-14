<?php

/**
 * Lớp cơ sở cho Cộng Tác Viên thông thường
 */
class AffiliatePartner {
    // Khai báo hằng số cho tên nền tảng
    const TEN_NEN_TANG = "Sọppe Affiliate";
    
    // Khai báo các thuộc tính protected để các lớp con có thể truy cập
    protected $name;            // Họ tên cộng tác viên
    protected $email;          // Email liên hệ
    protected $commissionRate; // Tỷ lệ hoa hồng (%)
    protected $isActive;       // Trạng thái hoạt động
    
    /**
     * Khởi tạo một cộng tác viên mới
     */
    public function __construct($name, $email, $commissionRate, $isActive = true) {
        $this->name = $name;
        $this->email = $email;
        $this->commissionRate = $commissionRate;
        $this->isActive = $isActive;
    }
    
    /**
     * Lấy tên cộng tác viên
     * Vì thuộc tính $name đc khai báo là protected nên không thể truy cập trực tiếp từ bên ngoài
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Tính hoa hồng dựa trên giá trị đơn hàng
     */
    public function calculateCommission($orderValue) {
        if (!$this->isActive) {
            return 0;
        }
        return ($this->commissionRate / 100) * $orderValue;
    }
    
    /**
     * Lấy thông tin tổng quan của cộng tác viên
     */
    public function getSummary() {
        $status = $this->isActive ? "Đang hoạt động" : "Ngừng hoạt động";
        return sprintf(
            "[%s] CTV: %s (Email: %s) - Hoa hồng: %.1f%% - Trạng thái: %s",
            self::TEN_NEN_TANG,
            $this->name,
            $this->email,
            $this->commissionRate,
            $status
        );
    }
    
    /**
     * Hàm hủy - được gọi khi đối tượng bị xóa
     */
    public function __destruct() {
        echo "CTV {$this->name} đã được giải phóng khỏi bộ nhớ.\n";
    }
}