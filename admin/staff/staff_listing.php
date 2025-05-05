 <!-- Giao diện chính trang quản lý nhân viên -->
 <?php
    require_once './connect_db.php';
    $db = new connect_db();

    // Lấy dữ liệu tìm kiếm từ form
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';

    // Xây dựng truy vấn tìm kiếm
    $sql = "SELECT * FROM staff WHERE 1=1";
    $params = [];

    if (!empty($id)) {
        $sql .= " AND mastaff = :id";
        $params['id'] = $id;
    }

    if (!empty($name)) {
        $sql .= " AND staffname LIKE :name";
        $params['name'] = "%$name%";
    }

    // Thực thi truy vấn
    $staffs = $db->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);

    $powergroups = $db->query("SELECT * FROM powergroup")->fetchAll(PDO::FETCH_ASSOC);
?>

 <link rel="stylesheet" href="./css/staff_styles.css?v=<?php echo time(); ?>">

 <div class="main-content">
     <h1>Danh sách nhân viên</h1>

     <div class="buttons">
         <a id="openAddModal" type="button" class="btn btn-primary btn-sm permission-them">Thêm nhân viên</a>
     </div>

     <div class="listing-search">
         <form id="staff-search-form" onsubmit="return searchStaff();">
             <fieldset>
                 <legend>Tìm kiếm nhân viên:</legend>
                 ID: <input type="text" name="id" id="search-id">
                 Tên nhân viên: <input type="text" name="name" id="search-name">
                 <input type="submit" value="Tìm">
                 <input type="button" value="Xóa bộ lọc" onclick="clearFilters()">
             </fieldset>
         </form>
     </div>

     <table>
         <thead>
             <tr>
                 <th>ID</th>
                 <th>Tên nhân viên</th>
                 <th>Email</th>
                 <th>Địa chỉ</th>
                 <th>Nhóm quyền</th>
                 <th>Thao tác</th>
             </tr>
         </thead>

         <tbody id="staff-list">
             <!-- Staff rows will be loaded here by AJAX -->
         </tbody>
     </table>
 </div>

 <!-- Modal chỉnh sửa -->
 <div class="modal" id="editModal" style="display:none;">
     <div class="modal-content">
         <span class="close-btn">&times;</span>
         <h3>Chỉnh sửa nhân viên</h3>
         <form id="editStaffForm">
             <input type="hidden" id="edit-id" name="id">

             <label for="edit-staffname">Tên nhân viên</label>
             <input type="text" id="edit-staffname" name="staffname" required>

             <label for="edit-email">Email</label>
             <input type="email" id="edit-email" name="email" required>

             <label for="edit-address">Địa chỉ</label>
             <input type="text" id="edit-address" name="address">

             <label for="edit-powergroupid">Nhóm quyền</label>
             <select id="edit-powergroupid" name="powergroupid">
                 <?php foreach ($powergroups as $group): ?>
                 <option value="<?php echo $group['powergroupid']; ?>"><?php echo $group['powergroupname']; ?></option>
                 <?php endforeach; ?>
             </select>

             <label for="edit-password">Mật khẩu mới (để trống nếu không đổi)</label>
             <input type="password" id="edit-password" name="password">

             <button type="submit" class="save-btn">Cập nhật</button>
         </form>
     </div>
 </div>

 <!-- Modal thêm nhân viên -->
 <div id="addModal" class="modal" style="display:none;">
     <div class="modal-content">
         <span class="close-btn">&times;</span>
         <h3>Thêm nhân viên</h3>
         <form id="addStaffForm">
             <label for="add-staffname">Tên nhân viên</label>
             <input type="text" id="add-staffname" name="staffname" required>

             <label for="add-email">Email</label>
             <input type="email" id="add-email" name="email" required>

             <label for="address">Địa chỉ</label>
             <input type="text" id="address" name="address">

             <label for="powergroupid">Nhóm quyền</label>
             <select id="powergroupid" name="powergroupid">
                 <?php foreach ($powergroups as $group): ?>
                 <option value="<?php echo $group['powergroupid']; ?>"><?php echo $group['powergroupname']; ?></option>
                 <?php endforeach; ?>
             </select>

             <label for="add-password">Mật khẩu</label>
             <input type="password" id="add-password" name="password" required>

             <button type="submit" class="save-btn">Thêm</button>
         </form>
     </div>
 </div>

 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script>
var currentPage = 1;
var currentFilters = {};

function searchStaff() {
    currentFilters.id = $('#search-id').val();
    currentFilters.name = $('#search-name').val();
    currentPage = 1;
    loadStaff(currentPage);
    return false;
}

function clearFilters() {
    $('#search-id').val('');
    $('#search-name').val('');
    currentFilters = {};
    currentPage = 1;
    loadStaff(currentPage);
}

function loadStaff() {
    $.ajax({
        url: 'staff/ajax.php?action=list',
        method: 'GET',
        data: {
            id: currentFilters.id || '',
            name: currentFilters.name || ''
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateStaffList(response.staffs);
            } else {
                alert('Không tìm thấy nhân viên.');
                $('#staff-list').html('');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Lỗi tải danh sách nhân viên:", textStatus, errorThrown, jqXHR.responseText);
            alert('Lỗi khi tải danh sách nhân viên. Vui lòng kiểm tra console để biết chi tiết.');
        }
    });
}

function updateStaffList(staffs) {
    const staffList = $('#staff-list');
    staffList.empty();
    staffs.forEach(staff => {
        const row = '<tr id="staff-' + staff.mastaff + '">' +
            '<td>' + staff.mastaff + '</td>' +
            '<td>' + staff.staffname + '</td>' +
            '<td>' + staff.email + '</td>' +
            '<td>' + (staff.address || '') + '</td>' +
            '<td>' + (staff.powergroupname || '') + '</td>' +
            '<td>' +
            '<button class="btn btn-warning btn-sm edit-btn permission-sua" ' +
            'data-id="' + staff.mastaff + '" ' +
            'data-staffname="' + (staff.staffname || '') + '" ' +
            'data-email="' + (staff.email || '') + '" ' +
            'data-address="' + (staff.address || '') + '" ' +
            'data-powergroupid="' + (staff.powergroupid || '') + '">' +
            'Sửa</button>' +
            '<button class="btn btn-danger btn-sm delete-btn permission-xoa" data-id="' + staff.mastaff +
            '">Xóa</button>' +
            '</td>' +
            '</tr>';
        staffList.append(row);
    });

    // Attach event listeners for edit and delete buttons
    $('.edit-btn').click(function() {
        const id = $(this).data('id');
        const staffname = $(this).data('staffname');
        const email = $(this).data('email');
        const address = $(this).data('address');
        const powergroupid = $(this).data('powergroupid');

        $('#edit-id').val(id);
        $('#edit-staffname').val(staffname);
        $('#edit-email').val(email);
        $('#edit-address').val(address);
        $('#edit-powergroupid').val(powergroupid);

        $('#editModal').show();
    });

    $('.delete-btn').click(function() {
        const id = $(this).data('id');
        if (confirm('Bạn có chắc muốn xóa nhân viên này?')) {
            $.ajax({
                url: 'staff/ajax.php?action=delete',
                method: 'GET',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#staff-' + id).remove();
                        loadStaff();
                        window.location.reload();
                    } else {
                        alert('Xóa không thành công, vui lòng thử lại!');
                    }
                },
                error: function() {
                    alert('Đã xảy ra lỗi khi xóa nhân viên.');
                }
            });
        }
    });
}

$(document).ready(function() {
    loadStaff();

    // Open add modal
    $('#openAddModal').click(function() {
        $('#addModal').show();
    });

    // Close modals
    $('.close-btn').click(function() {
        $(this).closest('.modal').hide();
    });

    // Close modal on outside click
    $(window).click(function(event) {
        if ($(event.target).hasClass('modal')) {
            $(event.target).hide();
        }
    });

    // Handle add staff form submission
    $('#addStaffForm').submit(function(e) {
        e.preventDefault();
        const formData = {
            staffname: $('#add-staffname').val(),
            email: $('#add-email').val(),
            address: $('#address').val(),
            powergroupid: $('#powergroupid').val(),
            password: $('#add-password').val()
        };
        $.ajax({
            url: 'staff/ajax.php?action=add',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    alert('Thêm nhân viên thành công');
                    $('#addModal').hide();
                    loadStaff();
                } else {
                    alert('Thêm nhân viên thất bại: ' + res.message);
                }
            },
            error: function() {
                alert('Lỗi khi thêm nhân viên');
            }
        });
    });

    // Handle edit staff form submission
    $('#editStaffForm').submit(function(e) {
        e.preventDefault();
        const formData = {
            id: $('#edit-id').val(),
            staffname: $('#edit-staffname').val(),
            email: $('#edit-email').val(),
            address: $('#edit-address').val(),
            powergroupid: $('#edit-powergroupid').val(),
            password: $('#edit-password').val()
        };
        $.ajax({
            url: 'staff/ajax.php?action=edit',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    alert('Cập nhật nhân viên thành công');
                    $('#editModal').hide();
                    loadStaff();
                    window.location.reload();
                } else {
                    alert('Cập nhật nhân viên thất bại: ' + res.message);
                }
            },
            error: function() {
                alert('Lỗi khi cập nhật nhân viên');
            }
        });
    });
});
 </script>