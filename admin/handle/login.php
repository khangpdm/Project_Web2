<?php 
require_once __DIR__ . '/../../admin/connect_db.php';

session_start();

$db = new connect_db();
$conn = $db->getConnection();

$response = ["status" => "error", "errors" => []];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['staffname']) && isset($_POST['password'])) {

        $staffname = $_POST['staffname'];
        $password = $_POST['password'];

        $check_sql = "SELECT * FROM staff WHERE staffname = :staffname";
        $stmt = $conn->prepare($check_sql);
        $stmt->bindParam(':staffname', $staffname);
        $stmt->execute();
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($staff) {
            // Temporary fix: direct password comparison instead of password_verify
            if (password_verify($password,$staff['password'])) {
                $response = ["status" => "success", "message" => "Đăng nhập thành công!"];
                $_SESSION['mastaff'] = $staff['mastaff'];
                $response['staffname'] = $staff['staffname'];
            } else {
                $response["errors"]["password"] = "Mật khẩu không chính xác!";
            }
        } else {
            $response["errors"]["staffname"] = "Tài khoản không tồn tại!";
        }
    }
} else {
    $response = ["status" => "error", "message" => "Invalid mode!"];
}

// Trả kết quả JSON
echo json_encode($response);
$conn = null;