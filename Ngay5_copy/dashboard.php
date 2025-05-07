<?php
require_once 'includes/config.php';
require_once 'includes/logger.php';

// Require login to access dashboard
requireLogin();

// Get statistics data
$stats = getDashboardStats();

// Update existing header
include 'includes/header.php';

/**
 * Get statistics for dashboard
 * 
 * @return array Statistics data
 */
function getDashboardStats() {
    $stats = [
        'today_logs' => 0,
        'week_logs' => 0,
        'total_logs' => 0,
        'total_uploads' => 0,
        'activity_by_day' => [],
        'recent_activities' => [],
        'recent_uploads' => []
    ];
    
    // Current date info
    $today = date('Y-m-d');
    $currentWeekStart = date('Y-m-d', strtotime('monday this week'));
    $currentWeekEnd = date('Y-m-d', strtotime('sunday this week'));
    
    // Scan logs directory
    if (is_dir('logs')) {
        $logFiles = scandir('logs');
        
        // Initialize activity by day array for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $stats['activity_by_day'][$date] = 0;
        }
        
        foreach ($logFiles as $file) {
            if (preg_match('/^log_(\d{4}-\d{2}-\d{2})\.txt$/', $file, $matches)) {
                $logDate = $matches[1];
                $logPath = "logs/$file";
                
                if (file_exists($logPath)) {
                    // Count lines in log file
                    $lineCount = count(file($logPath));
                    
                    // Update total logs count
                    $stats['total_logs'] += $lineCount;
                    
                    // Check if log is from today
                    if ($logDate === $today) {
                        $stats['today_logs'] = $lineCount;
                    }
                    
                    // Check if log is from current week
                    if ($logDate >= $currentWeekStart && $logDate <= $currentWeekEnd) {
                        $stats['week_logs'] += $lineCount;
                    }
                    
                    // Add to activity by day (last 7 days)
                    $sevenDaysAgo = date('Y-m-d', strtotime('-6 days'));
                    if ($logDate >= $sevenDaysAgo && isset($stats['activity_by_day'][$logDate])) {
                        $stats['activity_by_day'][$logDate] = $lineCount;
                    }
                    
                    // Get recent activities from today's log
                    if ($logDate === $today) {
                        $content = file_get_contents($logPath);
                        $lines = explode(PHP_EOL, $content);
                        $lines = array_filter($lines);
                        $lines = array_slice(array_reverse($lines), 0, 5);
                        $stats['recent_activities'] = $lines;
                    }
                }
            }
        }
    }
    
    // Count uploads
    if (is_dir('uploads')) {
        $uploadFiles = scandir('uploads');
        $stats['total_uploads'] = count(array_diff($uploadFiles, ['.', '..']));
        
        // Get recent uploads
        $uploads = [];
        foreach (array_diff($uploadFiles, ['.', '..']) as $file) {
            $filePath = "uploads/$file";
            if (file_exists($filePath)) {
                $uploads[] = [
                    'name' => $file,
                    'time' => filemtime($filePath),
                    'size' => filesize($filePath),
                    'type' => pathinfo($filePath, PATHINFO_EXTENSION)
                ];
            }
        }
        
        // Sort by time (newest first) and take 5
        usort($uploads, function($a, $b) {
            return $b['time'] - $a['time'];
        });
        
        $stats['recent_uploads'] = array_slice($uploads, 0, 5);
    }
    
    return $stats;
}

/**
 * Format bytes to human-readable size
 * 
 * @param int $bytes Bytes to format
 * @return string Formatted size
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Chào mừng, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h5>
                    <p class="card-text">Bảng điều khiển tổng quan hệ thống nhật ký hoạt động.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Hoạt động hôm nay</h5>
                    <p class="display-4"><?php echo $stats['today_logs']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Hoạt động tuần này</h5>
                    <p class="display-4"><?php echo $stats['week_logs']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Tổng hoạt động</h5>
                    <p class="display-4"><?php echo $stats['total_logs']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Tổng file tải lên</h5>
                    <p class="display-4"><?php echo $stats['total_uploads']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Hoạt động 7 ngày qua</h5>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Hoạt động gần đây</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($stats['recent_activities'])): ?>
                        <ul class="list-group">
                            <?php foreach ($stats['recent_activities'] as $activity): ?>
                                <li class="list-group-item small"><?php echo htmlspecialchars($activity); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Không có hoạt động nào hôm nay.</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="view_log.php" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">File tải lên gần đây</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($stats['recent_uploads'])): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tên file</th>
                                    <th>Thời gian</th>
                                    <th>Kích thước</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['recent_uploads'] as $upload): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($upload['name']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', $upload['time']); ?></td>
                                        <td><?php echo formatBytes($upload['size']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">Không có file nào được tải lên.</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="uploads_manager.php" class="btn btn-sm btn-primary">Quản lý file</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thao tác nhanh</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="index.php" class="btn btn-primary w-100">
                                <i class="bi bi-journal-plus"></i> Ghi nhật ký mới
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="view_log.php" class="btn btn-info w-100 text-white">
                                <i class="bi bi-journal-text"></i> Xem nhật ký
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="exports.php" class="btn btn-success w-100">
                                <i class="bi bi-file-earmark-arrow-down"></i> Xuất nhật ký
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="settings.php" class="btn btn-secondary w-100">
                                <i class="bi bi-gear"></i> Cài đặt
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart data
        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    <?php 
                    foreach (array_keys($stats['activity_by_day']) as $date) {
                        echo "'" . date('d/m', strtotime($date)) . "',";
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Số hoạt động',
                    data: [
                        <?php 
                        foreach ($stats['activity_by_day'] as $count) {
                            echo "$count,";
                        }
                        ?>
                    ],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>

</body>
</html>