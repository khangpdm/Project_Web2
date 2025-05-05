<!-- Xóa khách hàng -->
<?php
require_once '../connect_db.php';
$db = new connect_db();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Kiểm tra ID có tồn tại không
    $customer = $db->getById("customer", $id);
    if ($customer) {
        $db->delete("customer", $id);
    }
}

// Chuyển hướng sau khi xóa
header("Location: ../index.php?page=customer");
exit();
?>