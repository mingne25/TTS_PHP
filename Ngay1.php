<?php
// ====== HẰNG SỐ ======
const COMMISSION_RATE = 0.2;
const VAT_RATE = 0.1;

// ====== DỮ LIỆU ĐẦU VÀO ======
$campaignName = "Spring Sale 2025";
$productName = "Áo thời trang";
$productType = "Thời trang";
$campaignStatus = true;

// Giá trị đơn hàng tính theo $
$orderList = [
    "ID001" => 99.99,
    "ID002" => 39.99,
    "ID003" => 19.99,
    "ID004" => 99.99,
    "ID005" => 69.99,
];

// ====== TÍNH TOÁN DOANH THU ======
$orderKeys = array_keys($orderList);    // Lấy danh sách các khóa của mảng đơn hàng
$orderCount = count($orderList);        // Đếm số lượng đơn hàng
$revenue = 0;                           // Khởi tạo biến doanh thu

// Sử dụng vòng lặp for để tính tổng doanh thu
for ($i = 0; $i < $orderCount; $i++) {
    $revenue += $orderList[$orderKeys[$i]];     // Cộng doanh thu từ từng đơn hàng
}

// ====== TÍNH CHI PHÍ VÀ LỢI NHUẬN ======
$commissionCost = $revenue * COMMISSION_RATE;   // Tính chi phí hoa hồng
$vat = $revenue * VAT_RATE;                     // Tính thuế VAT
$profit = $revenue - $commissionCost - $vat;    // Tính lợi nhuận

// ====== ĐÁNH GIÁ ======
if ($profit > 0) {
    $result = "Chiến dịch thành công"; 
} elseif ($profit == 0) {
    $result = "Chiến dịch hòa vốn";
} else {
    $result = "Chiến dịch thất bại";
}

// ====== THÔNG BÁO DỰA THEO LOẠI SẢN PHẨM ======
switch ($productType) {
    case "Thời trang":
        $productMessage = "Sản phẩm Thời trang có doanh thu ổn định.";
        break;
    case "Điện tử":
        $productMessage = "Sản phẩm Điện tử có sức hút cao.";
        break;
    case "Gia dụng":
        $productMessage = "Sản phẩm Gia dụng bán đều.";
        break;
    default:
        $productMessage = "Loại sản phẩm chưa xác định.";
}

// ====== KẾT QUẢ ======
echo "Tên chiến dịch: " . $campaignName . "<br>";
echo "Trạng thái: " . ($campaignStatus ? "Đã kết thúc" : "Đang chạy") . "<br>";
echo "Tên sản phẩm: " . $productName . " ($productType)" . "<br>";
echo "Số lượng đơn hàng: " . $orderCount . "<br>";
echo "Tổng doanh thu: "  .$revenue. " $" . "<br>";
echo "Chi phí hoa hồng: "  .$commissionCost .  " $" . "<br>";
echo "Thuế VAT: " .$vat. " $" . "<br>";
echo "Lợi nhuận: " .$profit. " $" . "<br>";
echo "Đánh giá: " . $result ."<br>";
echo $productMessage . "<br>";

// ====== CHI TIẾT ĐƠN HÀNG ======
echo "Chi tiết đơn hàng " . "<br>";
echo "========================" . "<br>";
foreach ($orderList as $id => $value) {
    echo "$id: " .$value. " $" . "<br>";
}
echo "========================" . "<br>";

// ====== THÔNG BÁO CUỐI CÙNG ======
echo "Thông báo: Chiến dịch " . $campaignName . " " . ($campaignStatus ? "đã kết thúc" : "đang chạy") .  " với lợi nhuận: " .$profit.  " $" . "<br>";

// Magic constants dùng để debug
echo "File: " . __FILE__ . " - Line: " . __LINE__ . "<br>";
?>
