<?php
class OrderItem {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function add($orderId, $productId, $quantity, $price) {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, price_at_order_time) 
                 VALUES (?, ?, ?, ?)"
            );
            
            $result = $stmt->execute([
                (int)$orderId,
                (int)$productId,
                (int)$quantity,
                (float)$price
            ]);

            if ($result) {
                // Cập nhật số lượng tồn kho
                $updateStmt = $this->pdo->prepare(
                    "UPDATE products 
                     SET stock_quantity = stock_quantity - ? 
                     WHERE id = ?"
                );
                return $updateStmt->execute([(int)$quantity, (int)$productId]);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error adding order item: " . $e->getMessage());
            return false;
        }
    }

    public function checkStock($productId, $quantity) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT stock_quantity 
                 FROM products 
                 WHERE id = ? AND stock_quantity >= ?"
            );
            $stmt->execute([(int)$productId, (int)$quantity]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error checking stock: " . $e->getMessage());
            return false;
        }
    }
    public function getByOrder($orderId) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT oi.*, p.product_name 
                 FROM order_items oi
                 JOIN products p ON p.id = oi.product_id
                 WHERE oi.order_id = ?"
            );
            $stmt->execute([(int)$orderId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting order items: " . $e->getMessage());
            return [];
        }
    }   
}