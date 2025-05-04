<?php
require '../connect_db.php'; 

header('Content-Type: application/json'); // Thiết lập kiểu nội dung trả về là JSON

$response = ['success' => false, 'message' => '']; // Khởi tạo phản hồi mặc định

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $db = new connect_db(); // Tạo đối tượng connect_db

    try {
        // Kiểm tra số lượng sản phẩm trước khi xóa
        $product = $db->getById('product', $_GET['id']);
        if ($product === false) {
            $response['message'] = 'Sản phẩm không tồn tại.';
        } else {
            // Cập nhật số lượng sản phẩm về 0 thay vì xóa
            $sql = "UPDATE product SET soluong = 0 WHERE masp = :id";
            $result = $db->query($sql, ['id' => $_GET['id']]);
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Đã cập nhật số lượng sản phẩm về 0.';
            } else {
                $response['message'] = 'Không thể cập nhật số lượng sản phẩm.';
            }
        }
    } catch (PDOException $e) {
        $response['message'] = "Không thể xóa sản phẩm. Lỗi: " . $e->getMessage();
    }
} else {
    $response['message'] = 'ID không hợp lệ.';
}

// Trả về phản hồi JSON
echo json_encode($response);
?>
