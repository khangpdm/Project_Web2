<?php
require_once __DIR__ . '/../../admin/connect_db.php';

$config_name = "product";

// Kết nối cơ sở dữ liệu
$db = new connect_db(); // Tạo đối tượng kết nối

// Xử lý tìm kiếm qua AJAX
if (!empty($_POST)) {
    $_SESSION[$config_name . '_filter'] = $_POST;

    // Xây dựng điều kiện WHERE
    $where = "";
    $params = [];
    foreach ($_POST as $field => $value) {
        if (!empty($value)) {
            $where .= (!empty($where) ? " AND " : " WHERE ") . "`" . $field . "` LIKE :$field";
            $params[$field] = "%$value%"; // Thêm tham số vào mảng params
        }
    }

    // Truy vấn sản phẩm
    $productQuery = "SELECT * FROM `product`" . $where . " ORDER BY `id` DESC";
    $productStmt = $db->query($productQuery, $params);
    $products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

    // Trả về dữ liệu dưới dạng JSON
    echo json_encode(['success' => true, 'products' => $products]);
    exit;
}