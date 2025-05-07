<?php
// ==================== DỮ LIỆU GIẢ LẬP ====================
$employees = [
    ['id' => 101, 'name' => 'Nguyễn Văn A', 'base_salary' => 5000000],
    ['id' => 102, 'name' => 'Trần Thị B', 'base_salary' => 6000000],
    ['id' => 103, 'name' => 'Lê Văn C', 'base_salary' => 5500000],
];

$timesheet = [
    101 => ['2025-03-01', '2025-03-02', '2025-03-04', '2025-03-05'],
    102 => ['2025-03-01', '2025-03-03', '2025-03-04'],
    103 => ['2025-03-02', '2025-03-03', '2025-03-04', '2025-03-05', '2025-03-06'],
];

$adjustments = [
    101 => ['allowance' => 500000, 'deduction' => 200000],
    102 => ['allowance' => 300000, 'deduction' => 100000],
    103 => ['allowance' => 400000, 'deduction' => 150000],
];

define('STANDARD_DAYS', 22);

// ==================== HÀM TÁI SỬ DỤNG ====================
function getWorkingDays($timesheet) {
    $timesheet = array_map('array_unique', $timesheet); // Loại bỏ trùng lặp
    return array_map('count', $timesheet);
}

function calculateNetSalaries($employees, $working_days, $adjustments) {
    $salaries = [];
    foreach ($employees as $emp) {
        $id = $emp['id'];
        $days = $working_days[$id] ?? 0;
        $base = $emp['base_salary'];
        $allow = $adjustments[$id]['allowance'] ?? 0;
        $deduct = $adjustments[$id]['deduction'] ?? 0;
        $salaries[$id] = round(($base / STANDARD_DAYS) * $days + $allow - $deduct);
    }
    return $salaries;
}

function generatePayrollTable($employees, $working_days, $adjustments, $net_salaries) {
    return array_map(function ($emp) use ($working_days, $adjustments, $net_salaries) {
        $id = $emp['id'];
        return [
            'id' => $id,
            'name' => $emp['name'],
            'working_days' => $working_days[$id] ?? 0,
            'base_salary' => $emp['base_salary'],
            'allowance' => $adjustments[$id]['allowance'] ?? 0,
            'deduction' => $adjustments[$id]['deduction'] ?? 0,
            'net_salary' => $net_salaries[$id] ?? 0,
        ];
    }, $employees);
}

function findMaxMinWorkingDays($working_days) {
    $sorted = $working_days;
    asort($sorted);
    return [array_key_last($sorted), array_key_first($sorted)];
}

function getEmployeeNameById($employees, $id) {
    foreach ($employees as $emp) {
        if ($emp['id'] === $id) return $emp['name'];
    }
    return null;
}

function getQualifiedEmployees($working_days, $employees, $threshold = 4) {
    $qualified = array_filter($working_days, fn($days) => $days >= $threshold);
    return array_map(fn($id) => getEmployeeNameById($employees, $id), array_keys($qualified));
}

// ==================== 5. CẬP NHẬT DỮ LIỆU ====================
$new_employees = [
    ['id' => 104, 'name' => 'Phạm Thị D', 'base_salary' => 5800000],
];
$employees = array_merge($employees, $new_employees);

array_push($timesheet[101], '2025-03-06');
array_unshift($timesheet[101], '2025-02-28');
array_pop($timesheet[101]);
array_shift($timesheet[101]);

// ==================== XỬ LÝ ====================
$working_days = getWorkingDays($timesheet);
$net_salaries = calculateNetSalaries($employees, $working_days, $adjustments);
$payroll = generatePayrollTable($employees, $working_days, $adjustments, $net_salaries);
[$max_id, $min_id] = findMaxMinWorkingDays($working_days);
$max_name = getEmployeeNameById($employees, $max_id);
$min_name = getEmployeeNameById($employees, $min_id);
$qualified_names = getQualifiedEmployees($working_days, $employees);

$check_day = in_array('2025-03-03', $timesheet[102]);
$exists_adjustment = array_key_exists(101, $adjustments);

// ==================== 8. HTML HIỂN THỊ ====================
echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Bảng Lương Tháng 03/2025</title>
    <link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>
    <script src='https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js' defer></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
        }
        .animate-pulse:hover {
            animation: pulse 0.3s ease-in-out;
        }
        .bg-gradient {
            background: linear-gradient(135deg, #007BFF, #00C4B4);
        }
        .shadow-glow {
            box-shadow: 0 4px 20px rgba(0, 123, 255, 0.3);
        }
        .table-row:hover {
            background-color: #e6f3ff;
            transition: background-color 0.3s ease;
        }
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 24px rgba(0, 123, 255, 0.4);
        }
        /* Responsive adjustments */
        @media (max-width: 640px) {
            .table-container {
                overflow-x: auto;
            }
            .stat-card {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body class='bg-gray-100 font-sans'>
    <div class='min-h-screen bg-gradient'>
        <div class='container mx-auto px-4 py-12'>
            <!-- Header -->
            <div class='text-center mb-12 animate-fadeIn'>
                <h1 class='text-5xl font-extrabold text-white mb-4'>Bảng Lương Tháng 03/2025</h1>
                <p class='text-lg text-white opacity-80'>Quản lý lương chuyên nghiệp, minh bạch và hiện đại</p>
            </div>

            <!-- Stats Overview -->
            <div class='grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12'>";
                $total_payroll = array_sum($net_salaries);
echo "          <div class='stat-card bg-white rounded-xl p-6 shadow-glow animate-fadeIn' style='animation-delay: 0.2s'>
                    <h3 class='text-lg font-semibold text-gray-700'>Tổng Quỹ Lương</h3>
                    <p class='text-3xl font-bold text-blue-600'>" . number_format($total_payroll) . " VND</p>
                </div>
                <div class='stat-card bg-white rounded-xl p-6 shadow-glow animate-fadeIn' style='animation-delay: 0.4s'>
                    <h3 class='text-lg font-semibold text-gray-700'>Nhân Viên Siêng Nhất</h3>
                    <p class='text-xl font-medium text-blue-600'>$max_name ({$working_days[$max_id]} ngày)</p>
                </div>
                <div class='stat-card bg-white rounded-xl p-6 shadow-glow animate-fadeIn' style='animation-delay: 0.6s'>
                    <h3 class='text-lg font-semibold text-gray-700'>Nhân Viên Ít Nhất</h3>
                    <p class='text-xl font-medium text-blue-600'>$min_name ({$working_days[$min_id]} ngày)</p>
                </div>
            </div>

            <!-- Payroll Table -->
            <div class='table-container bg-white rounded-xl shadow-glow overflow-hidden animate-fadeIn' style='animation-delay: 0.8s'>
                <table class='w-full text-left'>
                    <thead class='bg-blue-600 text-white'>
                        <tr>
                            <th class='p-4'>Mã NV</th>
                            <th class='p-4'>Họ Tên</th>
                            <th class='p-4'>Ngày Công</th>
                            <th class='p-4'>Lương Cơ Bản</th>
                            <th class='p-4'>Phụ Cấp</th>
                            <th class='p-4'>Khấu Trừ</th>
                            <th class='p-4'>Lương Thực Lĩnh</th>
                        </tr>
                    </thead>
                    <tbody>";
foreach ($payroll as $p) {
    echo "              <tr class='table-row border-b border-gray-200'>
                            <td class='p-4'>{$p['id']}</td>
                            <td class='p-4'>{$p['name']}</td>
                            <td class='p-4'>{$p['working_days']}</td>
                            <td class='p-4'>" . number_format($p['base_salary']) . "</td>
                            <td class='p-4'>" . number_format($p['allowance']) . "</td>
                            <td class='p-4'>" . number_format($p['deduction']) . "</td>
                            <td class='p-4 font-bold text-blue-600'>" . number_format($p['net_salary']) . "</td>
                        </tr>";
}
echo "              </tbody>
                </table>
            </div>

            <!-- Qualified Employees -->
            <div class='mt-12 animate-fadeIn' style='animation-delay: 1s' x-data='{ open: false }'>
                <h2 class='text-2xl font-bold text-white mb-4 cursor-pointer' @click='open = !open'>
                    Nhân Viên Đủ Điều Kiện Xét Thưởng
                    <span x-show='!open' class='inline-block ml-2'>▼</span>
                    <span x-show='open' class='inline-block ml-2'>▲</span>
                </h2>
                <div x-show='open' x-transition class='bg-white rounded-xl p-6 shadow-glow'>
                    <ul class='list-disc pl-6'>";
foreach ($qualified_names as $name) {
    echo "              <li class='text-gray-700 mb-2'>$name</li>";
}
echo "              </ul>
                </div>
            </div>

            <!-- Additional Info -->
            <div class='mt-12 grid grid-cols-1 sm:grid-cols-2 gap-6 animate-fadeIn' style='animation-delay: 1.2s'>
                <div class='bg-white rounded-xl p-6 shadow-glow'>
                    <p class='text-gray-700'>Trần Thị B có đi làm vào ngày 2025-03-03: <span class='font-bold text-blue-600'>" . ($check_day ? "Có" : "Không") . "</span></p>
                </div>
                <div class='bg-white rounded-xl p-6 shadow-glow'>
                    <p class='text-gray-700'>Thông tin phụ cấp của nhân viên 101 tồn tại: <span class='font-bold text-blue-600'>" . ($exists_adjustment ? "Có" : "Không") . "</span></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>";
