<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');
include("../database/database.php");

$response = ['status' => 'error', 'message' => 'Unknown error'];

if (!isset($_SESSION['macustomer'])) {
    $response['message'] = 'Bạn cần đăng nhập để thực hiện hành động này!';
    echo json_encode($response);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$productId = isset($_POST['productId']) ? trim($_POST['productId']) : '';
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($action === 'get_product') {
    $sql = "SELECT masp, tensp, dongiasanpham, soluong FROM product WHERE masp = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $response['status'] = 'success';
        $response['product'] = $product;
        $response['quantity'] = isset($_SESSION['buy_now']['quantity']) ? $_SESSION['buy_now']['quantity'] : 1;
    } else {
        $response['message'] = 'Sản phẩm không tồn tại!';
    }
    $stmt->close();
} elseif ($action === 'prepare') {
    if ($quantity < 1) {
        $response['message'] = 'Số lượng phải lớn hơn hoặc bằng 1!';
        echo json_encode($response);
        exit;
    }

    $sql = "SELECT soluong FROM product WHERE masp = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        $response['message'] = 'Sản phẩm không tồn tại!';
        $stmt->close();
        echo json_encode($response);
        exit;
    }

    if ($product['soluong'] < $quantity) {
        $response['message'] = 'Số lượng yêu cầu vượt quá tồn kho!';
        $stmt->close();
        echo json_encode($response);
        exit;
    }

    $_SESSION['buy_now'] = [
        'productId' => $productId,
        'quantity' => $quantity
    ];

    $response['status'] = 'success';
    $response['message'] = 'Sản phẩm đã được chuẩn bị để mua ngay!';
    $stmt->close();
} else {
    $response['message'] = 'Hành động không hợp lệ!';
}

echo json_encode($response);
$conn->close();
?>