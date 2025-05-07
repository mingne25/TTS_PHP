<?php
/**
 * Hàm xử lý tải lên tệp
 * 
 * @param array $file Phần tử trong mảng $_FILES
 * @return array Thông tin trạng thái về quá trình tải lên
 */
function handleFileUpload($file) {
    $result = [
        'success' => false,
        'message' => '',
        'filename' => ''
    ];
    
    // Kiểm tra xem tệp có được tải lên đúng cách không
    if (!isset($file) || $file['error'] != UPLOAD_ERR_OK) {
        $result['message'] = 'Lỗi khi tải file lên.';
        return $result;
    }
    
    // Đảm bảo thư mục uploads tồn tại
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }
    
    // Lấy thông tin tệp
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileType = $file['type'];
    
    // Lấy phần mở rộng của tệp
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Kiểm tra kích thước tệp (tối đa 2MB)
    $maxSize = 2 * 1024 * 1024;
    if ($fileSize > $maxSize) {
        $result['message'] = 'Kích thước file không được vượt quá 2MB.';
        return $result;
    }
    
    // Kiểm tra phần mở rộng của tệp
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        $result['message'] = 'Chỉ chấp nhận file có định dạng: ' . implode(', ', $allowedExtensions);
        return $result;
    }
    
    // Tạo tên tệp duy nhất với timestamp
    $timestamp = time();
    $newFileName = 'upload_' . $timestamp . '_' . $fileName;
    $uploadPath = 'uploads/' . $newFileName;
    
    // Di chuyển tệp đã tải lên vào thư mục đích
    if (move_uploaded_file($fileTmp, $uploadPath)) {
        $result['success'] = true;
        $result['message'] = 'File đã được tải lên thành công.';
        $result['filename'] = $newFileName;
    } else {
        $result['message'] = 'Đã xảy ra lỗi khi lưu file.';
    }
    
    return $result;
}
?>