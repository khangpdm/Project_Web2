<!-- Xóa nhóm quyền -->
<?php
require_once '../connect_db.php';
$db = new connect_db();
if (isset($_GET['id'])){
    $powergroupid = $_GET['id'];
    
    $exist = $db->query("SELECT * FROM powergroup WHERE powergroupid = ?", [$powergroupid])->fetch(PDO::FETCH_ASSOC);

    
    if ($exist){
        $db->query("UPDATE powergroup set status=0 WHERE powergroupid = ?",[$powergroupid]);
    }
}

header("Location: ../index.php?page=phanquyen");
exit();