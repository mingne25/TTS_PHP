function confirmDelete(id) {
    if (confirm("Bạn có chắc muốn xóa sản phẩm này?")) {
        // Gửi yêu cầu xóa đến backend (thay bằng AJAX khi tích hợp PHP)
        alert("Xóa sản phẩm #" + id);
    }
}

function addItem() {
    const container = document.getElementById('order-items');
    const item = document.createElement('div');
    item.className = 'row mb-3';
    item.innerHTML = `
        <div class="col-md-5">
            <label for="product_id" class="form-label">Sản Phẩm</label>
            <select class="form-select product-select" name="product_id[]" >
                <option value="">Chọn sản phẩm</option>
                <?php foreach ($products as $prod): ?>
                <option value="<?= $prod['id'] ?>" data-price="<?= $prod['unit_price'] ?>">
                    <?= htmlspecialchars($prod['product_name']) ?> 
                    (<?= number_format($prod['unit_price']) ?> VNĐ - Còn: <?= $prod['stock_quantity'] ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="quantity" class="form-label">Số Lượng</label>
            <input type="number" class="form-control quantity-input" name="quantity[]" min="1" >
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger mt-4" onclick="removeItem(this)">Xóa</button>
        </div>
    `;
    container.appendChild(item);
}

function removeItem(button) {
    button.parentElement.parentElement.remove();
}