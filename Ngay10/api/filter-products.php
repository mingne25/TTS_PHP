<?php
require_once '../config/database.php';
header('Content-Type: application/json');

try {
    $sql = "SELECT * FROM products";
    $params = [];

    if (isset($_GET['category'])) {
        $sql .= " WHERE category = ?";
        $params[] = $_GET['category'];

        if (isset($_GET['brand'])) {
            $sql .= " AND brand = ?";
            $params[] = $_GET['brand'];
        }
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}