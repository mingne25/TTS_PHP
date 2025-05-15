<?php
require_once __DIR__ . '/../models/Product.php';
// Khởi tạo PDO connection
$productModel = new Product($pdo);
// Lấy danh sách sản phẩm
$products = $productModel->getAll();
?>