<?php
require_once __DIR__ . '/../../admin/connect_db.php';

echo '<link rel="stylesheet" href="../admin/css/order_styles.css">';

$db = new connect_db();

// Get filter parameters
$status = $_GET['status'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$city = $_GET['city'] ?? '';
$district = $_GET['district'] ?? '';

// Fetch orders with filters
$orders = $db->getOrdersWithFilters($status, $start_date, $end_date, $city, $district);
?>

<div class="main-content">
    <h1>Quản lý Đơn hàng</h1>
    <div class="listing-items">
        
        <!-- Filter Form -->
        <div class="listing-search">
            <form id="filterForm">
                <fieldset>
                    <legend>Tìm kiếm đơn hàng:</legend>
                    Tình trạng: 
                    <select name="status">
                        <option value="">Tất cả</option>
                        <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Chưa xác nhận</option>
                        <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Đã xác nhận</option>
                        <option value="2" <?= $status === '2' ? 'selected' : '' ?>>Đã giao</option>
                        <option value="3" <?= $status === '3' ? 'selected' : '' ?>>Đã huỷ</option>
                    </select>
                    Từ ngày: <input type="date" name="start_date" value="<?= $start_date ?>">
                    Đến ngày: <input type="date" name="end_date" value="<?= $end_date ?>">
                    <?php
                    $locations = $db->getUniqueLocations();
                    $cities = array_unique(array_column($locations, 'city'));
                    $districts = array_unique(array_column($locations, 'district'));
                    ?>
                    Thành phố:
                    <select name="city">
                        <option value="">Tất cả</option>
                        <?php foreach($cities as $c): ?>
                            <option value="<?= $c ?>" <?= $city === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach ?>
                    </select>
                    Quận:
                    <select name="district">
                        <option value="">Tất cả</option>
                        <?php foreach($districts as $d): ?>
                            <option value="<?= $d ?>" <?= $district === $d ? 'selected' : '' ?>><?= $d ?></option>
                        <?php endforeach ?>
                    </select>
                    <button type="submit">Lọc</button>
                </fieldset>
            </form>
        </div>

        <!-- Orders List Container -->
        <div id="orders-container">
            <?php include 'order_list_content.php'; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // AJAX Filter Form Submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        // Update URL with page parameter in header.php path
        const newUrl = '/1/Project_Web2/admin/header.php?page=donhang&' + formData;
        history.pushState(null, null, newUrl);
        
        // Load filtered content via AJAX with page parameter
        $.ajax({
            url: '../admin/order/order_list_content.php?page=donhang',
            type: 'GET',
            data: formData,
            success: function(response) {
                $('#orders-container').html(response);
                initStatusUpdateHandlers();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $('#orders-container').html(
                    '<div class="text-center py-4 text-danger">' +
                    'Lỗi khi tải dữ liệu. Chi tiết: ' + xhr.status + ' - ' + error +
                    '</div>'
                );
            }
        });
    });

    function initStatusUpdateHandlers() {
        // Your existing status update code here
        $('.update-status').off('click').on('click', function() {
            console.log('Update button clicked');
            const orderId = $(this).data('order-id');
            const currentStatus = parseInt($(this).data('current-status'));
            
            // Only allow forward status progression
            let options = '';
            if (currentStatus < 1) options += '<option value="1">Đã xác nhận</option>';
            if (currentStatus < 2) options += '<option value="2">Đã giao</option>';
            options += '<option value="3">Huỷ đơn</option>';
            
            Swal.fire({
                title: 'Cập nhật trạng thái',
                html: `<select id="statusSelect" class="swal2-select">${options}</select>`,
                showCancelButton: true,
                confirmButtonText: 'Cập nhật',
                cancelButtonText: 'Huỷ',
                focusConfirm: false,
                preConfirm: () => {
                    return {
                        status: $('#statusSelect').val()
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Attempting to update status to:', result.value.status);
                    $.post('/1/Project_Web2/admin/order/update_status.php', {
                        order_id: orderId,
                        status: result.value.status
                    }, function(response) {
                        console.log('Server response:', response);
                        if (response.success) {
                            Swal.fire({
                                title: 'Thành công',
                                text: 'Đã cập nhật trạng thái',
                                icon: 'success'
                            }).then(() => {
                                // Refresh the orders list after update
                                $('#filterForm').trigger('submit');
                            });
                        } else {
                            Swal.fire('Lỗi', response.message || 'Có lỗi xảy ra', 'error');
                        }
                    }).fail(function(xhr) {
                        Swal.fire('Lỗi', 'Không thể kết nối đến server: ' + xhr.status, 'error');
                    });
                }
            });
        });
    }
    
    // Initialize handlers on first load
    initStatusUpdateHandlers();
});
</script>
