<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');
ob_start();
include("../database/database.php");

ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', '../logs/php_errors.log');

$response = ['status' => 'error', 'message' => 'Unknown error'];

try {
    if (!isset($_SESSION['macustomer'])) {
        throw new Exception('Bạn cần đăng nhập để đặt hàng!');
    }

    $userId = $_SESSION['macustomer'];
    $receiverName = isset($_POST['receiver_name']) ? trim($_POST['receiver_name']) : '';
    $phoneNumber = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
    $addressDetail = isset($_POST['address_detail']) ? trim($_POST['address_detail']) : '';
    $provinceId = isset($_POST['city_id']) ? trim($_POST['city_id']) : null;
    $districtId = isset($_POST['district_id']) ? trim($_POST['district_id']) : null;
    $addressOption = isset($_POST['address_option']) ? trim($_POST['address_option']) : '';
    $paymentMethod = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : 'cod';

    if (!$receiverName || !$phoneNumber || !$addressDetail || !$provinceId || !$districtId) {
        throw new Exception('Vui lòng điền đầy đủ thông tin bắt buộc!');
    }

    if (!preg_match('/^\d{10}$/', $phoneNumber)) {
        throw new Exception('Số điện thoại không hợp lệ!');
    }

    if ($paymentMethod === 'online') {
        $cardNumber = isset($_POST['so-the']) ? trim($_POST['so-the']) : '';
        $expiryDate = isset($_POST['ngay-het-han']) ? trim($_POST['ngay-het-han']) : '';
        $cvv = isset($_POST['cvv']) ? trim($_POST['cvv']) : '';

        if (!$cardNumber || !$expiryDate || !$cvv) {
            throw new Exception('Vui lòng điền đầy đủ thông tin thẻ!');
        }

        if (!preg_match('/^[0-9]{6}$/', $cardNumber)) {
            throw new Exception('Số thẻ không hợp lệ!');
        }

        if (!preg_match('/^[0-9]{3}$/', $cvv)) {
            throw new Exception('Mã CVV không hợp lệ!');
        }
    }

    $isBuyNow = isset($_SESSION['buy_now']) && !empty($_SESSION['buy_now']);
    $cartId = null;
    $totalPrice = 0;
    $items = [];

    $conn->begin_transaction();

    if ($isBuyNow) {
        if (!isset($_SESSION['buy_now']['productId']) || !isset($_SESSION['buy_now']['quantity'])) {
            throw new Exception('Dữ liệu mua ngay không hợp lệ!');
        }

        $productId = $_SESSION['buy_now']['productId'];
        $quantity = (int)$_SESSION['buy_now']['quantity'];

        $sql = "SELECT masp, tensp, dongiasanpham, soluong FROM product WHERE masp = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();

        if (!$product) {
            throw new Exception('Sản phẩm không tồn tại!');
        }

        if ($product['soluong'] < $quantity) {
            throw new Exception('Số lượng yêu cầu vượt quá tồn kho!');
        }

        $totalPrice = $product['dongiasanpham'] * $quantity;
        $items[] = [
            'masp' => $product['masp'],
            'tensp' => $product['tensp'],
            'soluong' => $quantity,
            'dongiasanpham' => $product['dongiasanpham'],
            'thanhtien' => $product['dongiasanpham'] * $quantity
        ];

        $cartId = 'CART' . uniqid();
        $sqlCart = "INSERT INTO cart (magiohang, mauser, maorder) VALUES (?, ?, NULL)";
        $stmtCart = $conn->prepare($sqlCart);
        $stmtCart->bind_param("ss", $cartId, $userId);
        $stmtCart->execute();
        $stmtCart->close();

        $sqlProductCart = "INSERT INTO product_cart (masp, magiohang, soluong) VALUES (?, ?, ?)";
        $stmtProductCart = $conn->prepare($sqlProductCart);
        $stmtProductCart->bind_param("ssi", $productId, $cartId, $quantity);
        $stmtProductCart->execute();
        $stmtProductCart->close();
    } else {
        $sqlCart = "SELECT * FROM cart WHERE mauser = ? AND maorder IS NULL";
        $stmtCart = $conn->prepare($sqlCart);
        $stmtCart->bind_param("s", $userId);
        $stmtCart->execute();
        $resultCart = $stmtCart->get_result();

        if ($resultCart->num_rows === 0) {
            throw new Exception('Giỏ hàng của bạn đang trống hoặc đã được đặt hàng!');
        }

        $cart = $resultCart->fetch_assoc();
        $cartId = $cart['magiohang'];
        $stmtCart->close();

        $sqlItems = "SELECT pc.soluong, p.masp, p.tensp, p.dongiasanpham 
                     FROM product_cart pc 
                     JOIN product p ON pc.masp = p.masp 
                     WHERE pc.magiohang = ?";
        $stmtItems = $conn->prepare($sqlItems);
        $stmtItems->bind_param("s", $cartId);
        $stmtItems->execute();
        $resultItems = $stmtItems->get_result();

        while ($item = $resultItems->fetch_assoc()) {
            $totalPrice += $item['soluong'] * $item['dongiasanpham'];
            $items[] = [
                'masp' => $item['masp'],
                'tensp' => $item['tensp'],
                'soluong' => $item['soluong'],
                'dongiasanpham' => $item['dongiasanpham'],
                'thanhtien' => $item['soluong'] * $item['dongiasanpham']
            ];
        }
        $stmtItems->close();
    }

    $orderId = 'ORD' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
    $billId = 'BIL' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
    $payById = ($paymentMethod === 'online') ? 'PAY002' : 'PAY001';
    $paymentName = ($paymentMethod === 'online') ? 'Thanh toán trực tuyến' : 'Thanh toán khi nhận hàng';
    $orderDate = date('Y-m-d H:i:s');

    $paymentDetails = ($paymentMethod === 'online') ? json_encode([
        'card_number' => substr($cardNumber, -4),
        'expiry_date' => $expiryDate,
        'cvv' => '***'
    ]) : '{}';

    $sqlPayBy = "INSERT INTO payby (mapayby, paybyname, address, details) 
                 VALUES (?, ?, ?, ?) 
                 ON DUPLICATE KEY UPDATE address = ?, details = ?";
    $stmtPayBy = $conn->prepare($sqlPayBy);
    $fullAddress = "$addressDetail, $districtId, $provinceId";
    $stmtPayBy->bind_param("ssssss", $payById, $paymentName, $fullAddress, $paymentDetails, $fullAddress, $paymentDetails);
    $stmtPayBy->execute();
    $stmtPayBy->close();

    $sqlOrder = "INSERT INTO `order` (maorder, magiohang, mauser, status) VALUES (?, ?, ?, 1)";
    $stmtOrder = $conn->prepare($sqlOrder);
    $stmtOrder->bind_param("sss", $orderId, $cartId, $userId);
    $stmtOrder->execute();
    $stmtOrder->close();

    // Update INSERT statement to remove receiver_name, phone_number, bill_date from bill table
    $sqlBill = "INSERT INTO bill (mabill, macustomer, maorder, mapayby, tongtien) 
                VALUES (?, ?, ?, ?, ?)";
    $stmtBill = $conn->prepare($sqlBill);
    $stmtBill->bind_param("ssssi", $billId, $userId, $orderId, $payById, $totalPrice);
    $stmtBill->execute();
    $stmtBill->close();

    // Remove insertion into non-existent bill_details table
    // The following code inserting into bill_details table was commented out to fix error:
    /*
    $sqlBillDetails = "INSERT INTO bill_details (mabill, masp, tensp, soluong, dongia, thanhtien) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtBillDetails = $conn->prepare($sqlBillDetails);
    foreach ($items as $item) {
        $stmtBillDetails->bind_param("sssiii", $billId, $item['masp'], $item['tensp'], $item['soluong'], $item['dongiasanpham'], $item['thanhtien']);
        $stmtBillDetails->execute();
    }
    $stmtBillDetails->close();
    */

    $sqlUpdateCart = "UPDATE cart SET maorder = ? WHERE magiohang = ?";
    $stmtUpdateCart = $conn->prepare($sqlUpdateCart);
    $stmtUpdateCart->bind_param("ss", $orderId, $cartId);
    $stmtUpdateCart->execute();
    $stmtUpdateCart->close();

    $newCartId = 'CART' . uniqid();
    $insertNewCart = "INSERT INTO cart (magiohang, mauser, maorder) VALUES (?, ?, NULL)";
    $stmtNewCart = $conn->prepare($insertNewCart);
    $stmtNewCart->bind_param("ss", $newCartId, $userId);
    $stmtNewCart->execute();
    $stmtNewCart->close();

    // Always update customer information during order placement
    $sqlUpdateCustomer = "UPDATE customer 
                          SET name = ?, phone = ?, province_id = ?, district_id = ?, address_detail = ? 
                          WHERE macustomer = ?";
    $stmtUpdateCustomer = $conn->prepare($sqlUpdateCustomer);
    $stmtUpdateCustomer->bind_param("ssssss", $receiverName, $phoneNumber, $provinceId, $districtId, $addressDetail, $userId);
    $stmtUpdateCustomer->execute();
    $stmtUpdateCustomer->close();

    if ($isBuyNow) {
        unset($_SESSION['buy_now']);
    }

    $conn->commit();

    $response['status'] = 'success';
    $response['message'] = 'Đặt hàng thành công!';
    $response['invoice'] = [
        'bill_id' => $billId,
        'order_id' => $orderId,
        'order_date' => $orderDate,
        'receiver_name' => $receiverName,
        'phone_number' => $phoneNumber,
        'address' => $fullAddress,
        'total_price' => $totalPrice,
        'items' => $items,
        'payment_method' => $paymentName
    ];
} catch (Exception $e) {
    $conn->rollback();
    $response['message'] = $e->getMessage();
    error_log("Lỗi trong order.php: " . $e->getMessage());
}

ob_end_clean();
echo json_encode($response);
$conn->close();
?>