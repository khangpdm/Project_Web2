<!-- Thêm Phân quyền -->
<?php
require_once '../connect_db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new connect_db();

    // Nhận dữ liệu từ form
    $powergroupname = trim($_POST['powergroupname']);

    $check_permission_func_map = $_POST['permission_func_map'];

    $existingPowerGroup = $db->query("SELECT powergroupid FROM powergroup WHERE powergroupname = ?", [$powergroupname])->fetch();
    if ($existingPowerGroup) {
        die("Nhóm quyền đã tồn tại! Vui lòng chọn nhóm quyền khác.");
    }

    // Chuẩn bị dữ liệu để thêm vào database
    $powerGroupData = [
        'powergroupname' => $powergroupname
    ];

    // Thêm phân quyền
    $idLasted= $db->insertAndGetID("powergroup", $powerGroupData);
    var_dump($idLasted);

    if ($idLasted!=null) {
        // Chuyển hướng về danh sách phân quyền
        foreach($check_permission_func_map as $value){
            list($permissionid,$funcid) =explode('_',$value);
            $db->query("INSERT INTO powergroup_func_permission (powergroupid,funcid,permissionid) VALUES(?,?,?)",[$idLasted,$funcid,$permissionid]);
        }
        header("Location: ../index.php?page=phanquyen");
        exit();
    } else {
        echo "Lỗi khi thêm nhóm quyền.";
    }
}
?>