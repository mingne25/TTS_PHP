<?php
class Order {
    private $pdo;

    // Khởi tạo đối tượng Order với PDO connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Thêm đơn hàng mới
    // $date Ngày đặt hàng
    // $customer Tên khách hàng
    // $note Ghi chú đơn hàng
    public function add($date, $customer, $note = '') {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO orders (order_date, customer_name, note) 
                VALUES (?, ?, ?)"
            );
            $stmt->execute([$date, $customer, $note]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error adding order: " . $e->getMessage());
            throw $e;
        }
    }

    // Lấy tất cả đơn hàng
    public function getAll() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM orders ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting all orders: " . $e->getMessage());
            return [];
        }
    }


    // Tính tổng tiền của một đơn hàng
    // $order_id ID đơn hàng cần tính tổng
    public function getTotalAmount($order_id) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT SUM(quantity * price_at_order_time) 
                 FROM order_items 
                 WHERE order_id = ?"
            );
            $stmt->execute([(int)$order_id]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error calculating total amount: " . $e->getMessage());
            return false;
        }
    }

    // Lấy thông tin đơn hàng theo ID
    // $id ID đơn hàng cần lấy
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([(int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting order by ID: " . $e->getMessage());
            return false;
        }
    }
}