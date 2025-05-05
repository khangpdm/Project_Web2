<?php
require_once '../connect_db.php';
$db = new connect_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    if ($id === '') {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        exit;
    }

    $customer = $db->getById('customer', $id);
    if (!$customer) {
        echo json_encode(['success' => false, 'message' => 'Khách hàng không tồn tại']);
        exit;
    }

    // Toggle status: if current status is 1, set to 0; if 0, set to 1
    $newStatus = ($customer['status'] == 1) ? 0 : 1;

    $updated = $db->update('customer', ['status' => $newStatus], $id);

    if ($updated) {
        echo json_encode(['success' => true, 'newStatus' => $newStatus]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cập nhật trạng thái thất bại']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
}
