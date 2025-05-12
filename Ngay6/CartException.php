<?php
/**
 * Class CartException
 * Lớp xử lý ngoại lệ tùy chỉnh cho ứng dụng giỏ hàng
 */
class CartException extends Exception {
    /**
     * Constructor
     * 
     * @param string $message Thông báo lỗi
     * @param int $code Mã lỗi
     * @param Exception|null $previous Ngoại lệ trước đó
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        // Gọi constructor của lớp cha
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Chuỗi biểu diễn của ngoại lệ
     * 
     * @return string
     */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}