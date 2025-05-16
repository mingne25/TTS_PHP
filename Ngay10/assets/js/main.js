document.addEventListener('DOMContentLoaded', function() {
    // 1. Xử lý chi tiết sản phẩm
    window.getProductDetails = function(productId) {
        fetch(`api/product-details.php?id=${productId}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('productDetails').innerHTML = html;
                let modal = new bootstrap.Modal(document.getElementById('productModal'));
                modal.show();
            });
    }

    // 2. Xử lý thêm vào giỏ hàng
    window.addToCart = function(productId) {
        fetch('api/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ productId: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật số lượng trong giỏ hàng
                document.querySelector('.cart-count').textContent = data.cartCount;
                // Hiển thị thông báo thành công
                alert('Đã thêm sản phẩm vào giỏ hàng!');
            } else {
                alert(data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi thêm vào giỏ hàng');
        });
    };

    // 3. Xử lý lọc theo ngành hàng và thương hiệu
    // Xử lý lọc sản phẩm
    const categorySelect = document.getElementById('categorySelect');
    const brandSelect = document.getElementById('brandSelect');
    const productsGrid = document.getElementById('productsGrid');

    // Xử lý thay đổi category
    categorySelect.addEventListener('change', function() {
        const selectedCategory = this.value;
        brandSelect.disabled = !selectedCategory;
        
        if(!selectedCategory) {
            brandSelect.innerHTML = '<option value="">Chọn thương hiệu</option>';
            loadAllProducts();
            return;
        }

        // Load brands cho category đã chọn
        fetch(`api/get-brands.php?category=${selectedCategory}`)
            .then(response => response.json())
            .then(brands => {
                brandSelect.innerHTML = '<option value="">Chọn thương hiệu</option>' +
                    brands.map(brand => `
                        <option value="${brand}">${brand}</option>
                    `).join('');
            })
            .catch(error => {
                console.error('Error:', error);
                brandSelect.innerHTML = '<option value="">Lỗi tải thương hiệu</option>';
            });

        // Lọc sản phẩm theo category
        filterProducts(selectedCategory);
    });

    // Xử lý thay đổi brand
    brandSelect.addEventListener('change', function() {
        const selectedCategory = categorySelect.value;
        const selectedBrand = this.value;
        
        filterProducts(selectedCategory, selectedBrand);
    });

    // Hàm lọc sản phẩm
    function filterProducts(category, brand = '') {
        const url = brand 
            ? `api/filter-products.php?category=${category}&brand=${brand}`
            : `api/filter-products.php?category=${category}`;

        fetch(url)
            .then(response => response.json())
            .then(products => {
                if (products.length > 0) {
                    productsGrid.innerHTML = products.map(product => `
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="${product.image_url}" 
                                     class="card-img-top" 
                                     alt="${product.name}">
                                <div class="card-body">
                                    <h5 class="card-title">${product.name}</h5>
                                    <p class="card-text">Giá: ${product.price} $</p>
                                    <div class="d-flex gap-2">
                                        <button onclick="getProductDetails(${product.id})" 
                                                class="btn btn-info">Chi tiết</button>
                                        <button onclick="addToCart(${product.id})" 
                                                class="btn btn-primary">Thêm vào giỏ</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    productsGrid.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-info">
                                Không tìm thấy sản phẩm nào phù hợp.
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                productsGrid.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            Có lỗi xảy ra khi tải sản phẩm.
                        </div>
                    </div>
                `;
            });
    }

    // Hàm load tất cả sản phẩm
    function loadAllProducts() {
        fetch('api/filter-products.php')
            .then(response => response.json())
            .then(products => {
                productsGrid.innerHTML = products.map(product => `
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="${product.image_url}" 
                                 class="card-img-top" 
                                 alt="${product.name}">
                            <div class="card-body">
                                <h5 class="card-title">${product.name}</h5>
                                <p class="card-text">Giá: ${product.price} $</p>
                                <div class="d-flex gap-2">
                                    <button onclick="getProductDetails(${product.id})" 
                                            class="btn btn-info">Chi tiết</button>
                                    <button onclick="addToCart(${product.id})" 
                                            class="btn btn-primary">Thêm vào giỏ</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            });
    }

    // 4. Xử lý tìm kiếm real-time
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const loadingSpinner = document.querySelector('.loading');
    let searchTimeout;

    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            searchResults.innerHTML = '';
            loadingSpinner.style.display = 'none';
            return;
        }

        loadingSpinner.style.display = 'block';
        searchTimeout = setTimeout(() => {
            fetch(`api/search.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = data.map(product => `
                        <div class="product-item">
                            <img src="${product.image_url}" alt="${product.name}" width="50">
                            <div class="ms-3">
                                <div>${product.name}</div>
                                <div class="text-primary">${number_format(product.price)} VNĐ</div>
                            </div>
                        </div>
                    `).join('');
                })
                .finally(() => {
                    loadingSpinner.style.display = 'none';
                });
        }, 300);
    });

    // 5. Xử lý form poll
    const pollForm = document.getElementById('pollForm');
    if (pollForm) {
        pollForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const selected = document.querySelector('input[name="pollOption"]:checked');
            if (!selected) return;

            fetch('api/poll.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ option: selected.value })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('pollResults').innerHTML = Object.entries(data)
                    .map(([option, percentage]) => `
                        <div class="mb-2">
                            <div>${option}: ${percentage}%</div>
                            <div class="progress">
                                <div class="progress-bar" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `).join('');
            });
        });
    }

    // Hàm hỗ trợ format số
    function number_format(number) {
        return new Intl.NumberFormat('vi-VN').format(number);
    }

    // 6. Handle product reviews
    window.getReviews = function(productId) {
        const reviewsContainer = document.getElementById('reviews');
        reviewsContainer.innerHTML = '<div class="text-center"><div class="spinner-border"></div></div>';

        fetch(`api/reviews.php?productId=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const { reviews, stats } = data;
                    let html = `
                        <div class="reviews-summary mb-3">
                            <h5>Product Reviews</h5>
                            <div class="d-flex align-items-center gap-2">
                                <div class="h4 mb-0">${stats.average_rating}</div>
                                <div class="text-muted">
                                    (${stats.total_reviews} reviews)
                                </div>
                            </div>
                        </div>
                    `;

                    if (reviews.length > 0) {
                        html += '<div class="reviews-list">' +
                            reviews.map(review => `
                                <div class="review-item border-bottom py-2">
                                    <div class="d-flex justify-content-between">
                                        <strong>${review.user_name}</strong>
                                        <small class="text-muted">${review.review_date}</small>
                                    </div>
                                    <div class="rating">
                                        ${'★'.repeat(review.rating)}${'☆'.repeat(5-review.rating)}
                                    </div>
                                    <p class="mb-0">${review.comment}</p>
                                </div>
                            `).join('') +
                            '</div>';
                    } else {
                        html += '<p class="text-center text-muted">No reviews yet</p>';
                    }

                    reviewsContainer.innerHTML = html;
                }
            })
            .catch(error => {
                reviewsContainer.innerHTML = `
                    <div class="alert alert-danger">
                        Error loading reviews: ${error.message}
                    </div>
                `;
            });
    };    
});