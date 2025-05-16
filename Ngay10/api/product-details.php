<?php
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        echo <<<HTML
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{$product['name']}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img src="{$product['image_url']}" class="img-fluid mb-3">
                <p>Mô tả: {$product['description']}</p>
                <p class="h4">Giá: {$product['price']} $</p>
                <p>Kho: {$product['stock']} sản phẩm</p>
                <button onclick="addToCart({$product['id']})" class="btn btn-primary">
                    Thêm vào giỏ hàng
                </button>
                <div id="reviews" class="mt-3"></div>
                <button onclick="getReviews({$product['id']})" class="btn btn-secondary">
                    Xem đánh giá
                </button>
            </div>
        </div>
        HTML;
    }
}
