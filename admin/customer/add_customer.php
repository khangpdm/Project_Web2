<!-- Thêm khách hàng -->
<?php
require_once __DIR__ . '/../../admin/connect_db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new connect_db();

    // Nhận dữ liệu từ form
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Kiểm tra email đã tồn tại chưa
    $existingCustomer = $db->query("SELECT macustomer FROM customer WHERE email = ?", [$email])->fetch();
    if ($existingCustomer) {
        die("Email đã tồn tại! Vui lòng chọn email khác.");
    }

    // Chuẩn bị dữ liệu để thêm vào database
    $customerData = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'password' => $password
    ];

    // Thêm khách hàng
    if ($db->insert("customer", $customerData)) {
        // Chuyển hướng về danh sách khách hàng
        header("Location: ../index.php?page=customer");
        exit();
    } else {
        echo "Lỗi khi thêm khách hàng.";
    }
}
?>