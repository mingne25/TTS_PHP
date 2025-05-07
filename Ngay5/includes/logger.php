<?php
/**
 * Hàm ghi lại hoạt động của người dùng
 * 
 * @param string $action Mô tả hành động của người dùng
 * @param string $uploadedFile Tên tệp được tải lên (nếu có)
 * @return bool Trả về true nếu ghi log thành công, false nếu thất bại
 */
function logActivity($action, $uploadedFile = null) {
    // Lấy ngày hiện tại để đặt tên tệp log
    $currentDate = date('Y-m-d');
    $logFile = "logs/log_$currentDate.txt";
    
    // Đảm bảo thư mục logs tồn tại
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    // Lấy thời gian hiện tại để ghi vào log
    $timestamp = date('Y-m-d H:i:s');
    
    // Lấy địa chỉ IP của người dùng
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    
    // Xây dựng nội dung log
    $logMessage = "[$timestamp] - IP: $ipAddress - Hành động: $action";
    
    // Thêm thông tin tệp tải lên nếu có
    if ($uploadedFile) {
        $logMessage .= " - Tệp: $uploadedFile";
    }
    
    $logMessage .= PHP_EOL;
    
    // Ghi nội dung vào tệp log
    return file_put_contents($logFile, $logMessage, FILE_APPEND) !== false;
}

/**
 * Hàm lấy nội dung log của một ngày cụ thể
 * 
 * @param string $date Ngày theo định dạng Y-m-d
 * @return string|false Nội dung log hoặc false nếu tệp không tồn tại
 */
function getLogContent($date) {
    $logFile = "logs/log_$date.txt";
    
    if (!file_exists($logFile)) {
        return false;
    }
    
    return file_get_contents($logFile);
}

/**
 * Hàm định dạng một dòng log với màu sắc dựa trên từ khóa
 * 
 * @param string $logEntry Dòng log cần định dạng
 * @return string Dòng log đã được định dạng với HTML
 */
function formatLogEntry($logEntry) {
    // Các từ khóa cần làm nổi bật
    $dangerWords = ['thất bại', 'lỗi', 'error', 'xóa', 'delete', 'đăng xuất thất bại'];
    $warningWords = ['cảnh báo', 'warning', 'chỉnh sửa', 'edit', 'sửa đổi'];
    $successWords = ['thành công', 'success', 'đăng nhập', 'login', 'xác thực'];
    
    // Kiểm tra từ khóa nguy hiểm
    foreach ($dangerWords as $word) {
        if (stripos($logEntry, $word) !== false) {
            return '<div class="log-danger">' . htmlspecialchars($logEntry) . '</div>';
        }
    }
    
    // Kiểm tra từ khóa cảnh báo
    foreach ($warningWords as $word) {
        if (stripos($logEntry, $word) !== false) {
            return '<div class="log-warning">' . htmlspecialchars($logEntry) . '</div>';
        }
    }
    
    // Kiểm tra từ khóa thành công
    foreach ($successWords as $word) {
        if (stripos($logEntry, $word) !== false) {
            return '<div class="log-success">' . htmlspecialchars($logEntry) . '</div>';
        }
    }
    
    // Định dạng mặc định
    return '<div>' . htmlspecialchars($logEntry) . '</div>';
}

/**
 * Hàm tìm kiếm từ khóa trong tệp log
 * 
 * @param string $date Ngày theo định dạng Y-m-d
 * @param string $keyword Từ khóa cần tìm
 * @return array|false Các dòng log khớp hoặc false nếu tệp không tồn tại
 */
function searchLogEntries($date, $keyword) {
    $logFile = "logs/log_$date.txt";
    
    if (!file_exists($logFile)) {
        return false;
    }
    
    $matches = [];
    $handle = fopen($logFile, 'r');
    
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            if (stripos($line, $keyword) !== false) {
                $matches[] = $line;
            }
        }
        fclose($handle);
    }
    
    return !empty($matches) ? $matches : false;
}
?>