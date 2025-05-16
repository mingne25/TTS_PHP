<?php
session_start();
require_once 'includes/header.php';
require_once 'config/database.php';
?>

<div class="container my-4">
    <!-- Search Section -->
    <div class="row mb-4">
        <div class="col-md-6 position-relative">
            <input type="text" id="searchInput" class="form-control" 
                   placeholder="Tìm kiếm sản phẩm...">
            <div class="loading spinner-border spinner-border-sm"></div>
            <div id="searchResults" class="bg-white border rounded"></div>
        </div>
    </div>

    <!-- Category and Brand Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h4>Lọc sản phẩm</h4>
            <div class="d-flex gap-3">
                <select id="categorySelect" class="form-select">
                    <option value="">Chọn ngành hàng</option>
                    <option value="Electronics">Điện tử</option>
                    <option value="Fashion">Thời trang</option>
                </select>
                <select id="brandSelect" class="form-select" disabled>
                    <option value="">Chọn thương hiệu</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row" id="productsGrid">
        <?php
        $stmt = $pdo->query("SELECT * FROM products LIMIT 6");
        while ($product = $stmt->fetch(PDO::FETCH_ASSOC)): 
        ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                     class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>" style="height: 500px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="card-text">Giá: <?= ($product['price']) ?> $</p>
                    <div class="d-flex gap-2">
                        <button onclick="getProductDetails(<?= $product['id'] ?>)" 
                                class="btn btn-info">Chi tiết</button>
                        <button onclick="addToCart(<?= $product['id'] ?>)" 
                                class="btn btn-primary">Thêm vào giỏ</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Poll Section -->
    <div class="row mt-5">
        <div class="col-md-6">
            <h4>Giúp chúng tôi cải thiện!</h4>
            <form id="pollForm">
                <div class="form-check">
                    <input type="radio" name="pollOption" value="interface" 
                           class="form-check-input" required>
                    <label class="form-check-label">Giao diện</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="pollOption" value="speed" 
                           class="form-check-input">
                    <label class="form-check-label">Tốc độ</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="pollOption" value="service" 
                           class="form-check-input">
                    <label class="form-check-label">Dịch vụ khách hàng</label>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Gửi đánh giá</button>
            </form>
            <div id="pollResults" class="mt-3"></div>
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div id="productDetails"></div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>