<?php
header('Content-Type: application/json; charset=UTF-8');
include("../database/database.php");

$response = ['status' => 'error', 'message' => 'Unknown error'];

try {
    $provinceId = isset($_GET['province_id']) ? trim($_GET['province_id']) : '';
    $districtId = isset($_GET['district_id']) ? trim($_GET['district_id']) : '';

    if (!$provinceId || !$districtId) {
        throw new Exception('Thiếu province_id hoặc district_id!');
    }

    $sqlProvince = "SELECT name FROM provinces WHERE province_id = ?";
    $stmtProvince = $conn->prepare($sqlProvince);
    $stmtProvince->bind_param("s", $provinceId);
    $stmtProvince->execute();
    $resultProvince = $stmtProvince->get_result();
    $province = $resultProvince->fetch_assoc();
    $stmtProvince->close();

    if (!$province) {
        throw new Exception('Không tìm thấy tỉnh/thành phố!');
    }

    $sqlDistrict = "SELECT name FROM districts WHERE district_id = ?";
    $stmtDistrict = $conn->prepare($sqlDistrict);
    $stmtDistrict->bind_param("s", $districtId);
    $stmtDistrict->execute();
    $resultDistrict = $stmtDistrict->get_result();
    $district = $resultDistrict->fetch_assoc();
    $stmtDistrict->close();

    if (!$district) {
        throw new Exception('Không tìm thấy quận/huyện!');
    }

    $response['status'] = 'success';
    $response['province_name'] = $province['name'];
    $response['district_name'] = $district['name'];
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>