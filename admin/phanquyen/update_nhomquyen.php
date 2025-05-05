<!-- Cập nhật thông tin nhóm quyền -->
<?php
require_once '../connect_db.php';
$db = new connect_db();

if ($_SERVER['REQUEST_METHOD']=="POST"){
    $check_permission_func_map = $_POST['permission_func_map'];
    $powergroupid = $_POST['powergroupid'];
    $db->query("DELETE FROM powergroup_func_permission WHERE powergroupid = ?",[$powergroupid]);

    foreach($check_permission_func_map as $value){
        list($permissionid,$funcid) = explode('_',$value); 
        $db->query("INSERT INTO powergroup_func_permission (powergroupid,funcid,permissionid) VALUES(?,?,?)",[$powergroupid,$funcid,$permissionid]);
    }

    $db->query("UPDATE powergroup SET last_updated=CURRENT_TIMESTAMP() WHERE powergroupid = ?", [$powergroupid]);

    header(header: "Location: ../index.php?page=phanquyen");
    exit();
}