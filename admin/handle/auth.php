<?php
require_once __DIR__ . '/../../admin/connect_db.php';

session_start();

header('Content-Type: application/json');

$db = new connect_db();
$conn = $db->getConnection();

if ($_POST['action'] === 'check') {
    if (isset($_SESSION['mastaff'])) {
        $mastaff = $_SESSION['mastaff'];
        $check_sql = "SELECT * FROM staff where mastaff = :mastaff";
        $stmt = $conn->prepare($check_sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'SQL error']);
            exit();
        }
        $stmt->bindParam(':mastaff', $mastaff);
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'SQL execute error']);
            exit();
        }
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($staff) {
            $_SESSION['staffname'] = $staff['staffname'];
            echo json_encode(["loggedIn" => true, "staffname" => $staff['staffname']]);
        } else {
            echo json_encode(["loggedIn" => false]);
        }
    } else {
        echo json_encode(["loggedIn" => false]);
    }
    exit();
} else if ($_POST['action'] === 'logout') {
    session_unset();
    session_destroy();
    echo json_encode(["loggedIn" => false]);
    exit();
}else if($_POST['action'] === 'check_powergroup'){
    if (isset($_SESSION['mastaff'])){
        $mastaff = $_SESSION['mastaff'];

        $check_sql = "SELECT pfp.funcid, pfp.permissionid FROM staff s LEFT JOIN powergroup_func_permission pfp ON s.powergroupid = pfp.powergroupid WHERE s.mastaff = :mastaff";
        $stmt = $conn->prepare($check_sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'SQL error']);
            exit();
        }
        $stmt->bindParam(':mastaff', $mastaff);
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'SQL execute error']);
            exit();
        }
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = array();
        if (count($result) > 0){
            foreach($result as $powergroupid){
               $permissionid = $powergroupid['permissionid'];
               $funcid = $powergroupid['funcid'];
               if (!isset($response[$permissionid]))
                    $response[$permissionid] = array();
                $response[$permissionid][] = $funcid;
            }
        }
        echo json_encode($response);
    }else{
        http_response_code(401);  // Mã lỗi 401 (Unauthorized)
        echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập. Vui lòng đăng nhập trước.']);
    }
}

$conn = null;
exit();
?>