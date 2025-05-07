<?php
// Bao gồm các tệp cần thiết
require_once 'includes/logger.php';

// Mặc định sử dụng ngày hiện tại nếu không có ngày nào được chỉ định
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// Lấy nội dung nhật ký
$logContent = [];
$hasResults = false;

if (!empty($searchKeyword)) {
    // Tìm kiếm từ khóa trong nhật ký
    $searchResults = searchLogEntries($selectedDate, $searchKeyword);
    
    if ($searchResults !== false) {
        $logContent = $searchResults;
        $hasResults = true;
    }
} else {
    // Lấy toàn bộ nội dung nhật ký của ngày được chọn
    $fullLogContent = getLogContent($selectedDate);
    
    if ($fullLogContent !== false) {
        $logContent = explode(PHP_EOL, $fullLogContent);
        // Loại bỏ các dòng trống
        $logContent = array_filter($logContent, function($line) {
            return !empty(trim($line));
        });
        $hasResults = true;
    }
}

// Bao gồm phần header
include 'includes/header.php';
?>

    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Xem nhật ký hoạt động</h5>
                    <?php if ($hasResults): ?>
                        <span class="badge bg-info"><?php echo count($logContent); ?> bản ghi</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="date" class="form-label">Chọn ngày</label>
                            <input type="date" class="form-control" id="date" name="date" value="<?php echo $selectedDate; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">Tìm kiếm (tùy chọn)</label>
                            <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($searchKeyword); ?>" placeholder="Nhập từ khóa...">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Xem</button>
                        </div>
                    </form>

                    <hr>

                    <div class="log-content mt-4">
                        <?php if ($hasResults): ?>
                            <div class="card">
                                <div class="card-header bg-light">
                                    <strong>Nhật ký ngày: <?php echo $selectedDate; ?></strong>
                                    <?php if (!empty($searchKeyword)): ?>
                                        <span class="badge bg-warning text-dark ms-2">Tìm kiếm: <?php echo htmlspecialchars($searchKeyword); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <div class="log-entries">
                                        <?php foreach ($logContent as $entry): ?>
                                            <?php echo formatLogEntry($entry); ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <?php if (!empty($searchKeyword)): ?>
                                    Không tìm thấy kết quả nào cho từ khóa "<?php echo htmlspecialchars($searchKeyword); ?>" trong nhật ký ngày <?php echo $selectedDate; ?>.
                                <?php else: ?>
                                    Không có nhật ký cho ngày <?php echo $selectedDate; ?>.
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>