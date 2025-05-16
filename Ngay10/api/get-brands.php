<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (isset($_GET['category'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT DISTINCT brand 
            FROM products 
            WHERE category = ?
            ORDER BY brand
        ");
        $stmt->execute([$_GET['category']]);
        $brands = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo json_encode($brands);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Category parameter is required']);
}