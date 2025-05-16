<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (isset($_GET['productId'])) {
    try {
        // Get reviews
        $stmt = $pdo->prepare("
            SELECT 
                r.id,
                r.user_name,
                r.rating,
                r.comment,
                DATE_FORMAT(r.created_at, '%d/%m/%Y %H:%i') as review_date
            FROM reviews r
            WHERE r.product_id = ?
            ORDER BY r.created_at DESC
        ");
        
        $stmt->execute([$_GET['productId']]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get stats
        $stmt = $pdo->prepare("
            SELECT 
                COALESCE(AVG(rating), 0) as average_rating,
                COUNT(*) as total_reviews
            FROM reviews 
            WHERE product_id = ?
        ");
        
        $stmt->execute([$_GET['productId']]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Format average rating to 1 decimal place
        $stats['average_rating'] = number_format($stats['average_rating'], 1);
        
        echo json_encode([
            'success' => true,
            'reviews' => $reviews,
            'stats' => $stats
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Product ID is required'
    ]);
}