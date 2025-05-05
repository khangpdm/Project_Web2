<?php
    require_once 'connect_db.php';
    $db = new connect_db();

    // Lấy các powergroup và chức năng tương ứng
    $sql = "SELECT pg.*, pfp.funcid, pfp.permissionid
            FROM powergroup pg 
            LEFT JOIN powergroup_func_permission pfp on pg.powergroupid=pfp.powergroupid
            WHERE 1=1";
    $params = [];

    $sql.= " AND pg.status != 0"; // Xóa rồi thì không hiển thị

    $powergroups_raw= $db->query($sql,$params)->fetchAll(PDO::FETCH_ASSOC);

    $powergroups = [];

    foreach($powergroups_raw as $row){
        $id = $row['powergroupid']; // vì có thể nhiều dòng trùng powergroupid
        if (!isset($powergroups[$id])){
            $powergroups[$id] = [
                'powergroupid' => $row['powergroupid'],
                'powergroupname' => $row['powergroupname'],
                'status' => $row['status'],
                'created_time' => $row['created_time'],
                'last_updated' => $row['last_updated'],
                'permission_func_map' => []
            ];
        }
        if (!empty($row['funcid']) && !empty($row['permissionid'])) {
            $powergroups[$id]['permission_func_map'][] = [
                'permission' => $row['permissionid'],
                'func' => $row['funcid']
            ];
        }
    }

    // Lấy các chức năng
    $sql = "SELECT * FROM func WHERE 1=1";

    $funcs = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);


    // Lấy các tên quyền
    $sql = "SELECT * FROM permission WHERE 1=1";

    $permissions = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

?>
<link rel="stylesheet" href="./css/customer_styles.css?v=<?php echo time(); ?>">

<div class="main-content">
    <h1>Phân quyền người dùng</h1>
    <div class="buttons">
        <a id="openAddModal" class="permission-them">Thêm nhóm quyền</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên nhóm quyền</th>
                <th>trạng thái</th>
                <th>Thời gian tạo</th>
                <th>Update gần đây nhất</th>
                <th>Thao tác</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($powergroups as $powergroup) : ?>
            <tr>
                <td><?= $powergroup['powergroupid']; ?></td>
                <td><?= $powergroup['powergroupname']; ?></td>
                <td><?= ($powergroup['status'] == 1)? "Hoạt động":"Tạm dừng"; ?></td>
                <td><?= $powergroup['created_time']; ?></td>
                <td><?= $powergroup['last_updated']; ?></td>
                <td>
                    <button type="button" class="btn btn-warning btn-sm edit-btn permission-sua"
                        data-powergroupid="<?= $powergroup['powergroupid']; ?>"
                        data-powergroupname="<?= $powergroup['powergroupname']; ?>"
                        data-mapping="<?= htmlspecialchars(json_encode($powergroup['permission_func_map']), ENT_QUOTES, 'UTF-8'); ?>">
                        Sửa
                    </button>

                    <a href="phanquyen/delete_phanquyen.php?id=<?= $powergroup['powergroupid']; ?>"
                        class="btn btn-danger btn-sm permission-xoa"
                        onclick="return confirm('Bạn có chắc muốn xóa không?');">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal thêm nhóm quyền -->
<div class="modal" id="addModal" style="display:none;">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h3>Thêm nhóm quyền</h3>
        <form action="phanquyen/add_nhomquyen.php" method="POST">
            <label for="powergroupname">Tên nhóm quyền</label>
            <input type="text" id="powergroupname" name="powergroupname" required>

            <table>
                <thead>
                    <tr>
                        <th>Tên quyền</th>
                        <?php foreach($funcs as $func) :?>
                        <th><?=htmlspecialchars(string: $func['funcname']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($permissions as $permission) :?>
                    <tr>
                        <td><?= htmlspecialchars($permission['permissionname']); ?></td>
                        <?php foreach($funcs as $func) :?>
                        <td>
                            <input type="checkbox" name="permission_func_map[]"
                                value="<?=$permission['permissionid'] . '_' . $func['funcid'] ?>">
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="save-btn">Thêm</button>
        </form>
    </div>
</div>

<!-- Modal chỉnh sửa -->
<div class="modal" id="editModal" style="display:none;">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h3>Chỉnh sửa Nhóm quyền</h3>
        <form action="phanquyen/update_nhomquyen.php" method="POST">
            <input type="hidden" id="edit-id" name="powergroupid">

            <label for="edit-name">Tên nhóm quyền</label>
            <input type="text" id="edit-name" name="powergroupname">

            <table>
                <thead>
                    <tr>
                        <th>Tên quyền</th>
                        <?php foreach($funcs as $func) :?>
                        <th><?=htmlspecialchars(string: $func['funcname']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($permissions as $permission) :?>
                    <tr>
                        <td><?= htmlspecialchars($permission['permissionname']); ?></td>
                        <?php foreach($funcs as $func) :?>
                        <td>
                            <input type="checkbox" name="permission_func_map[]"
                                value="<?=$permission['permissionid'] . '_' . $func['funcid'] ?>">
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="save-btn">Cập nhật</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
window.initEventListeners = function() {

    let addModal = document.getElementById("addModal");
    let editModal = document.getElementById("editModal");

    let openAddModalBtn = document.getElementById("openAddModal");
    let editButtons = document.querySelectorAll(".edit-btn");

    let closeButtons = document.querySelectorAll(".close-btn");

    // Mở modal thêm nhóm quyền
    if (openAddModalBtn) {
        openAddModalBtn.addEventListener("click", function(event) {
            $('#addModal').fadeIn(200);
        });
    }

    // Đóng các modal
    if (closeButtons.length > 0) {
        closeButtons.forEach(button => {
            button.addEventListener("click", function() {
                $(this).closest(".modal").fadeOut(200);
            });
        });
    }

    // Chỉnh sửa nhóm quyền
    if (editButtons.length > 0) {
        editButtons.forEach(button => {
            button.addEventListener("click", function(event) {
                event.preventDefault();

                // Lấy giá trị data từ button
                document.getElementById("edit-id").value = this.dataset.powergroupid;
                document.getElementById("edit-name").value = this.dataset.powergroupname;

                // Lấy mapping JSON từ data-mapping
                let mappingJSON = this.dataset.mapping;
                console.log('Mapping JSON:', mappingJSON); // Kiểm tra xem dữ liệu có tồn tại

                // Nếu có mapping, parse thành mảng đối tượng
                let mapping = JSON.parse(mappingJSON);
                console.log('Parsed Mapping:', mapping); // In ra mảng đối tượng đã được parse

                // Mở modal
                $('#editModal').fadeIn(20, () => {
                    // Sau khi fadeIn xong, mới xử lý các checkbox

                    // Xóa tất cả checkbox đã check
                    document.querySelectorAll(
                            `#editModal input[name="permission_func_map[]"]`)
                        .forEach(cb => cb.checked = false);

                    // Tự động check lại những checkbox theo mapping
                    mapping.forEach(pair => {
                        let value = `${pair.permission}_${pair.func}`;
                        let checkbox = document.querySelector(
                            `#editModal input[type="checkbox"][value="${value}"]`
                        );
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                });
            });
        });
    }



    // Đóng modal nếu click ngoài vùng modal
    document.querySelectorAll('.modal-content').forEach(modalContent => {
        modalContent.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });

    if (document.querySelector('.modal')) {
        window.addEventListener("click", function(event) {
            if (event.target.classList.contains("modal")) {
                event.target.style.display = "none";
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", function() {
    window.initEventListeners();
});
</script>