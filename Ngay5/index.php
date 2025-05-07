<?php
// Bao gồm các tệp cần thiết
require_once 'includes/logger.php';
require_once 'includes/upload.php';

// Khởi tạo các biến
$message = '';
$messageType = '';
$uploadedFile = null;

// Xử lý khi gửi biểu mẫu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy hành động từ biểu mẫu
    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    
    // Xử lý tải lên tệp nếu có
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadResult = handleFileUpload($_FILES['attachment']);
        
        if ($uploadResult['success']) {
            $uploadedFile = $uploadResult['filename'];
            $message = $uploadResult['message'];
            $messageType = 'success';
        } else {
            $message = $uploadResult['message'];
            $messageType = 'danger';
        }
    }
    
    // Ghi nhật ký hoạt động
    if (!empty($action)) {
        if (logActivity($action, $uploadedFile)) {
            $message .= ' Hoạt động đã được ghi nhật ký thành công.';
            $messageType = 'success';
        } else {
            $message .= ' Không thể ghi nhật ký hoạt động.';
            $messageType = 'danger';
        }
    } else {
        $message = 'Vui lòng nhập mô tả hành động.';
        $messageType = 'warning';
    }
}

// Bao gồm phần header
include 'includes/header.php';
?>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Ghi nhật ký hoạt động</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="action" class="form-label">Mô tả hành động *</label>
                            <input type="text" class="form-control" id="action" name="action">
                            <div class="form-text">Mô tả ngắn gọn về hành động đang thực hiện.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="attachment" class="form-label">File minh chứng (tùy chọn)</label>
                            <input type="file" class="form-control" id="attachment" name="attachment">
                            <div class="form-text">Kích thước tối đa: 2MB. Định dạng: JPG, PNG, PDF.</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Ghi nhật ký</button>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Các hành động mẫu</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <button type="button" class="list-group-item list-group-item-action" onclick="setAction('Đăng nhập vào hệ thống')">Đăng nhập vào hệ thống</button>
                        <button type="button" class="list-group-item list-group-item-action" onclick="setAction('Đăng xuất khỏi hệ thống')">Đăng xuất khỏi hệ thống</button>
                        <button type="button" class="list-group-item list-group-item-action" onclick="setAction('Gửi biểu mẫu đánh giá')">Gửi biểu mẫu đánh giá</button>
                        <button type="button" class="list-group-item list-group-item-action" onclick="setAction('Tải tài liệu lên')">Tải tài liệu lên</button>
                        <button type="button" class="list-group-item list-group-item-action" onclick="setAction('Chỉnh sửa thông tin cá nhân')">Chỉnh sửa thông tin cá nhân</button>
                        <button type="button" class="list-group-item list-group-item-action" onclick="setAction('Xóa tài liệu')">Xóa tài liệu</button>
                        <button type="button" class="list-group-item list-group-item-action" onclick="setAction('Đăng nhập thất bại')">Đăng nhập thất bại</button>
                        <button type="button" class="list-group-item list-group-item-action" onclick="setAction('Cảnh báo bảo mật')">Cảnh báo bảo mật</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Hàm đặt giá trị cho ô nhập mô tả hành động
        function setAction(actionText) {
            document.getElementById('action').value = actionText;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>