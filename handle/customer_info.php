<?php
session_start();

header('Content-Type: application/json; charset=UTF-8'); // Đảm bảo phản hồi là JSON với charset UTF-8

// Kiểm tra đăng nhập
if (!isset($_SESSION['macustomer'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng đăng nhập để xem thông tin'
    ]);
    exit();
}

include("../database/database.php");

// Kiểm tra kết nối cơ sở dữ liệu
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Không thể kết nối đến cơ sở dữ liệu: ' . ($conn->connect_error ?? 'Lỗi không xác định')
    ]);
    exit();
}

$user_id = $_SESSION['macustomer'];

// Truy vấn thông tin tài khoản
$query = "SELECT c.*, p.name AS province_name, d.name AS district_name 
          FROM customer c 
          LEFT JOIN provinces p ON c.province_id = p.province_id 
          LEFT JOIN districts d ON c.district_id = d.district_id 
          WHERE c.macustomer = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi chuẩn bị truy vấn SQL: ' . $conn->error
    ]);
    $conn->close();
    exit();
}

$stmt->bind_param("s", $user_id);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi thực thi truy vấn SQL: ' . $stmt->error
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Không tìm thấy thông tin tài khoản'
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

// Truy vấn danh sách tỉnh/thành phố
$provinces_query = "SELECT province_id, name FROM provinces ORDER BY name ASC";
$provinces_result = $conn->query($provinces_query);
if (!$provinces_result) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi truy vấn danh sách tỉnh/thành phố: ' . $conn->error
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

$provinces = [];
while ($row = $provinces_result->fetch_assoc()) {
    $provinces[] = [
        'province_id' => $row['province_id'],
        'name' => $row['name']
    ];
}

// Truy vấn danh sách quận/huyện
$districts_query = "SELECT district_id, name, province_id FROM districts ORDER BY name ASC";
$districts_result = $conn->query($districts_query);
if (!$districts_result) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi truy vấn danh sách quận/huyện: ' . $conn->error
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

$districts = [];
while ($row = $districts_result->fetch_assoc()) {
    $districts[] = [
        'district_id' => $row['district_id'],
        'name' => $row['name'],
        'province_id' => $row['province_id']
    ];
}

// Trả về phản hồi JSON
echo json_encode([
    'status' => 'success',
    'data' => [
        'username' => $user['username'] ?? 'Chưa cập nhật',
        'name' => $user['name'] ?? 'Chưa cập nhật',
        'email' => $user['email'] ?? 'Chưa cập nhật',
        'phone' => $user['phone'] ?? 'Chưa cập nhật',
        'province_id' => $user['province_id'] ?? '',
        'province_name' => $user['province_name'] ?? 'Chưa chọn',
        'district_id' => $user['district_id'] ?? '',
        'district_name' => $user['district_name'] ?? 'Chưa chọn',
        'address_detail' => $user['address_detail'] ?? ''
    ],
    'provinces' => $provinces,
    'districts' => $districts
], JSON_UNESCAPED_UNICODE); // Hỗ trợ tiếng Việt trong JSON

$stmt->close();
$conn->close();
exit();
?>