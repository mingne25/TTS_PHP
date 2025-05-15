<?php
require_once __DIR__ . '/../config/db.php';

class Product {
    private $pdo;
    
    // Khởi tạo đối tượng Product với PDO connection
    
    public function __construct($pdo) { 
        $this->pdo = $pdo; 
    }


    // Lấy danh sách sản phẩm có phân trang
    // $limit Số sản phẩm mỗi trang
    // $offset Vị trí bắt đầu
    public function getAll($limit = 10, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM products ORDER BY id DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting all products: " . $e->getMessage());
            return [];
        }
    }


    // Lấy sản phẩm có giá lớn hơn giá cho trước
    // $minPrice Giá tối thiểu
    public function getByPrice($minPrice) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM products WHERE unit_price > ?");
            $stmt->execute([(float)$minPrice]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting products by price: " . $e->getMessage());
            return [];
        }
    }

    // Lấy sản phẩm sắp xếp theo giá giảm dần
    public function getByPriceDesc() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM products ORDER BY unit_price DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting products by price desc: " . $e->getMessage());
            return [];
        }
    }

    // Thêm sản phẩm mới
    // $name Tên sản phẩm
    // $price Giá sản phẩm
    // $stock Số lượng tồn kho
    public function add($name, $price, $stock) {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO products (product_name, unit_price, stock_quantity) 
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([$name, (float)$price, (int)$stock]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error adding product: " . $e->getMessage());
            return false;
        }
    }

    // Lấy thông tin sản phẩm theo ID
    // $id ID sản phẩm
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting product by ID: " . $e->getMessage());
            return false;
        }
    }


    // Cập nhật thông tin sản phẩm
    // $id ID sản phẩm
    // $name Tên mới
    // $price Giá mới
    // $stock Số lượng tồn kho mới
    public function update($id, $name, $price, $stock) {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE products 
                 SET product_name = ?, 
                     unit_price = ?, 
                     stock_quantity = ? 
                 WHERE id = ?"
            );
            return $stmt->execute([$name, (float)$price, (int)$stock, (int)$id]);
        } catch (PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    // Xóa sản phẩm theo ID
    // $id ID sản phẩm cần xóa
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
            return $stmt->execute([(int)$id]);
        } catch (PDOException $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }

    // Lấy danh sách sản phẩm mới nhất
    // $limit Số lượng sản phẩm cần lấy
    public function getLatest($limit = 5) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM products ORDER BY id DESC LIMIT :limit");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting latest products: " . $e->getMessage());
            return [];
        }
    }

    // Kiểm tra sản phẩm đã được đặt hàng chưa
    // $id ID sản phẩm cần kiểm tra
    public function isInOrder($id) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM order_items WHERE product_id = ?"
            );
            $stmt->execute([(int)$id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error checking product in orders: " . $e->getMessage());
            return false;
        }
    }
}