<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');
include("../database/database.php");

$response = ['status' => 'error', 'message' => 'Unknown error'];

if (!isset($_SESSION['macustomer'])) {
    $response['message'] = 'Bạn cần đăng nhập để cập nhật thông tin!';
    echo json_encode($response);
    exit;
}

$userId = $_SESSION['macustomer'];

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$provinceId = isset($_POST['province_id']) ? trim($_POST['province_id']) : null;
$districtId = isset($_POST['district_id']) ? trim($_POST['district_id']) : null;
$addressDetail = isset($_POST['address_detail']) ? trim($_POST['address_detail']) : null;

// Kiểm tra email và phone không được để trống
if (empty($email) || empty($phone)) {
    $response['message'] = 'Email và số điện thoại không được để trống!';
    echo json_encode($response);
    exit;
}

// Chuyển chuỗi rỗng thành null (đề phòng trường hợp JavaScript không xử lý)
$provinceId = $provinceId === '' ? null : $provinceId;
$districtId = $districtId === '' ? null : $districtId;
$addressDetail = $addressDetail === '' ? null : $addressDetail;

// Cập nhật thông tin tài khoản
$sql = "UPDATE customer 
        SET email = ?, phone = ?, province_id = ?, district_id = ?, address_detail = ? 
        WHERE macustomer = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $response['message'] = 'Lỗi SQL: ' . $conn->error;
    echo json_encode($response);
    exit;
}

// Xử lý giá trị null khi bind_param
// bind_param không chấp nhận trực tiếp giá trị null, cần chuyển thành chuỗi hoặc giá trị hợp lệ
$stmt->bind_param(
    "ssssss",
    $email,
    $phone,
    $provinceId,
    $districtId,
    $addressDetail,
    $userId
);

if (!$stmt->execute()) {
    $response['message'] = 'Lỗi thực thi SQL: ' . $stmt->error;
    echo json_encode($response);
    exit;
}

$response['status'] = 'success';
$response['message'] = 'Cập nhật thông tin tài khoản thành công!';
echo json_encode($response);

$stmt->close();
$conn->close();
?>