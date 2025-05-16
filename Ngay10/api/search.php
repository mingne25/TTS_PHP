<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (isset($_GET['q'])) {
    $search = '%' . $_GET['q'] . '%';
    $stmt = $pdo->prepare("
        SELECT id, name, price, image_url 
        FROM products 
        WHERE name LIKE ? 
        OR description LIKE ? 
        LIMIT 5
    ");
    $stmt->execute([$search, $search]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}