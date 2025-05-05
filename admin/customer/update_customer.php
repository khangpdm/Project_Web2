<!-- Cập nhật thông tin khách hàng -->
<?php
require_once '../connect_db.php';
$db = new connect_db();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    // $trang_thai = $_POST['trang_thai']; // Not used as no such column
    // $vai_tro = $_POST['vai_tro'];
    $password = $_POST['password'] ?? ''; // Mật khẩu mới (nếu có)

    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // Nếu không có mật khẩu mới, giữ nguyên mật khẩu cũ
        $password = $db->getById("customer", $id)['password'];
    }

    // Cập nhật thông tin khách hàng
    $db->update("customer", [
        "name" => $name,
        "email" => $email,
        "phone" => $phone,
        // "vai_tro" => $vai_tro,
        "password" => $password
    ], $id);

    // Chuyển hướng về danh sách khách hàng
    header("Location: ../index.php?page=customer");
    exit();
}
?>
