<?php
require_once __DIR__ . '/../../admin/connect_db.php';
include 'phantrang.php';

$config_name = "product";
$config_title = "sản phẩm";

$db = new connect_db();

function buildSearchWhereClause(&$params) {
    $where = "";
    if (!empty($_GET['id'])) {
        $where .= (!empty($where) ? " AND " : " WHERE ") . "`masp` LIKE :id";
        $params['id'] = "%" . $_GET['id'] . "%";
    }
    if (!empty($_GET['name'])) {
        $where .= (!empty($where) ? " AND " : " WHERE ") . "`tensp` LIKE :name";
        $params['name'] = "%" . $_GET['name'] . "%";
    }
    return $where;
}

$item_per_page = (!empty($_GET['per_page'])) ? (int)$_GET['per_page'] : 5;
$current_page = max(1, (!empty($_GET['pages'])) ? (int)$_GET['pages'] : 1);

$params = [];
$where = buildSearchWhereClause($params);

$offset = ($current_page - 1) * $item_per_page;

$totalQuery = "SELECT COUNT(*) FROM `product`" . $where;
$totalStmt = $db->query($totalQuery, $params);
$totalRecordsCount = $totalStmt->fetchColumn();

$pagination = new Pagination($current_page, $totalRecordsCount, $item_per_page);

$productQuery = "SELECT p.*, p.tensp AS name, pt.tenloaisp FROM `product` p LEFT JOIN producttype pt ON p.maloaisp = pt.maloaisp" . $where . " ORDER BY p.`masp` DESC LIMIT $offset, $item_per_page";
$productStmt = $db->query($productQuery, $params);
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
body,
h1,
p,
ul {
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    color: #333;
}

.main-content h1 {
    text-align: center;
    margin: 20px 0;
    color: #2c3e50;
}

.listing-items {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.buttons {
    text-align: right;
    margin-bottom: 20px;
}

.buttons a {
    background: #006ADD;
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 5px;
}

.buttons a:hover {
    background: #2980b9;
}

.listing-search {
    margin-bottom: 20px;
}

.listing-search input[type="text"] {
    padding: 8px;
    margin-right: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

ul {
    list-style-type: none;
}

.listing-item-heading {
    background: #eaeaea;
    font-weight: bold;
    padding: 10px;
}

.listing-prop {
    display: inline-block;
    width: 12%;
    padding: 10px;
    text-align: center;
}

.listing-prop img {
    max-width: 100px;
    max-height: 100px;
    border-radius: 5px;
}

.listing-button a {
    display: inline-block;
    background: #e74c3c;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
}

.listing-button a:hover {
    background: #c0392b;
}

.listing-time {
    color: #7f8c8d;
}

.clear-both {
    clear: both;
}

.total-items {
    text-align: center;
    margin: 20px 0;
    font-style: italic;
}

.pagination {
    text-align: center;
    margin-top: 20px;
}

.pagination a {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 5px;
    background: #006ADD;
    color: white;
    border-radius: 4px;
    text-decoration: none;
}

.pagination a:hover {
    background: #2980b9;
}
</style>

<div class="main-content">
    <h1>Danh sách <?= htmlspecialchars($config_title) ?></h1>
    <div class="listing-items">
        <div class="buttons">
            <a href="./product/<?= htmlspecialchars($config_name)  ?>_edit.php" class="permission-them">Thêm
                <?= htmlspecialchars($config_title) ?></a>
        </div>
        <div class="listing-search">
            <form id="<?= htmlspecialchars($config_name) ?>-search-form" onsubmit="return searchProducts();">
                <fieldset>
                    <legend>Tìm kiếm <?= htmlspecialchars($config_title) ?>:</legend>
                    ID: <input type="text" name="id" id="search-id"
                        value="<?= !empty($_SESSION[$config_name . '_filter']['id']) ? htmlspecialchars($_SESSION[$config_name . '_filter']['id']) : "" ?>" />
                    Tên <?= htmlspecialchars($config_title) ?>: <input type="text" name="name" id="search-name"
                        value="<?= !empty($_SESSION[$config_name . '_filter']['name']) ? htmlspecialchars($_SESSION[$config_name . '_filter']['name']) : "" ?>" />
                    <input type="submit" value="Tìm" />
                    <input type="button" value="Xóa bộ lọc" onclick="clearFilters()" />
                </fieldset>
            </form>
        </div>
        <div class="total-items">
            <span>Có tất cả <strong><?= htmlspecialchars($totalRecordsCount) ?></strong>
                <?= htmlspecialchars($config_title) ?> trên
                <strong><?= htmlspecialchars($pagination->total_pages) ?></strong> trang</span>
        </div>
        <ul id="product-list">
            <li class="listing-item-heading">
                <div class="listing-prop listing-img">Ảnh</div>
                <div class="listing-prop listing-name">Tên <?= htmlspecialchars($config_title) ?></div>
                <div class="listing-prop">Loại sản phẩm</div>
                <div class="listing-prop listing-button permission-xoa">Xóa</div>
                <div class="listing-prop listing-button permission-sua">Sửa</div>
                <div class="listing-prop listing-button">Ghi chú</div>
                <div class="listing-prop listing-time">Ngày tạo</div>
                <div class="listing-prop listing-time">Ngày cập nhật</div>
                <div class="clear-both"></div>
            </li>
            <?php foreach ($products as $row) { ?>
            <li id="product-<?= htmlspecialchars($row['masp']) ?>">
                <div class="listing-prop listing-img">
                    <img src="../admin/<?= htmlspecialchars($row['image'] ?? '') ?>"
                        alt="<?= htmlspecialchars($row['name'] ?? '') ?>"
                        title="<?= htmlspecialchars($row['name'] ?? '') ?>" />
                </div>
                <div class="listing-prop listing-name"><?= htmlspecialchars($row['name'] ?? '') ?></div>
                <div class="listing-prop"><?= htmlspecialchars($row['tenloaisp'] ?? '') ?></div>
                <div class="listing-prop listing-button permission-xoa">
                    <?php if ($row['soluong'] == 0) { ?>
                    <span style="color: red; font-weight: bold;">Đã bán hết</span>
                    <?php } else { ?>
                    <a href="javascript:void(0);"
                        onclick="removeRow('<?= htmlspecialchars($row['masp']) ?>', './product/ajax.php?action=delete')">Xóa</a>
                    <?php } ?>
                </div>

                <div class="listing-prop listing-button permission-sua">
                    <a
                        href="./product/<?= htmlspecialchars($config_name) ?>_edit.php?id=<?= htmlspecialchars($row['masp']) ?>">Sửa</a>
                </div>

                <div class="listing-prop listing-time"><?= date('d/m/Y H:i', strtotime($row['created_time'])) ?></div>
                <div class="listing-prop listing-time"><?= date('d/m/Y H:i', strtotime($row['last_updated'])) ?></div>
                <div class="clear-both"></div>
            </li>
            <?php } ?>
        </ul>

        <!-- Phần phân trang -->
        <div class="pagination" id="pagination">
            <!-- Pagination links will be inserted here by JavaScript -->
        </div>
        <div id="loading" style="display:none; text-align:center;">
            <img src="../img/loading.gif" alt="Loading..." />
        </div>

        <div class="clear-both"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function removeRow(id, url) {
    if (confirm('Bạn có chắc không?')) {
        $.ajax({
            url: url,
            data: {
                id: id
            },
            method: 'GET',
            dataType: 'JSON',
            success: function(res) {
                console.log(res);
                if (res.success) {
                    // Xóa phần tử tương ứng khỏi giao diện
                    $('#product-' + id).remove(); // Mỗi dòng có một ID là product-[id]
                    // Reload product list to update total count and pagination
                    loadProducts(currentPage);
                } else {
                    alert('Xóa không thành công, vui lòng thử lại!');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error details: ", textStatus, errorThrown);
                alert('Đã xảy ra lỗi: ' + textStatus);
            }
        });
    }
}

var currentPage = 1; // Current page
var currentFilters = {}; // Current filters

function searchProducts() {
    currentFilters.id = document.getElementById('search-id').value;
    currentFilters.name = document.getElementById('search-name').value;
    currentPage = 1;
    loadProducts(currentPage);
    return false;
}

function clearFilters() {
    document.getElementById('search-id').value = '';
    document.getElementById('search-name').value = '';
    currentFilters = {};
    currentPage = 1;
    loadProducts(currentPage);
}

function loadProducts(page) {
    currentPage = page;
    $.ajax({
        url: './product/ajax.php?action=pagination',
        method: 'GET',
        data: {
            page: page,
            per_page: 5,
            id: currentFilters.id || '',
            name: currentFilters.name || ''
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateProductList(response.products);
                updatePagination(page, response.totalPages);
                // Update total product count display
                $('.total-items span').html(
                    `Có tất cả <strong>${response.totalRecordsCount}</strong> <?= htmlspecialchars($config_title) ?> trên <strong>${response.totalPages}</strong> trang`
                );
            } else {
                alert('No products found.');
                $('#product-list').html('');
                $('#pagination').html('');
            }
            checkPowerGroup();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX error loading products:", textStatus, errorThrown, jqXHR.responseText);
            alert('Error loading products. Check console for details.');
        }
    });
}

var config_name = "product";

function updateProductList(products) {
    const productList = document.getElementById('product-list');
    const productTitle = "<?= htmlspecialchars($config_title) ?>";
    const header = `
        <ul id="product-list">
            <li class="listing-item-heading">
                <div class="listing-prop listing-img">Ảnh</div>
                <div class="listing-prop listing-name">Tên <?= htmlspecialchars($config_title) ?></div>
                <div class="listing-prop">Loại sản phẩm</div>
                <div class="listing-prop listing-button permission-xoa">Xóa</div>
                <div class="listing-prop listing-button permission-sua">Sửa</div>
                <div class="listing-prop listing-button">Ghi chú</div>
                <div class="listing-prop listing-time">Ngày tạo</div>
                <div class="listing-prop listing-time">Ngày cập nhật</div>
                <div class="clear-both"></div>
            </li>
    `;
    productList.innerHTML = header;
    products.forEach(row => {
        const li = document.createElement('li');
        li.id = "product-" + row.masp;
        li.innerHTML = `
            <div class="listing-prop listing-img">
                <img src="../admin/${row.image}" alt="${row.name}" title="${row.name}" />
            </div>
            <div class="listing-prop listing-name">${row.name}</div>
            <div class="listing-prop listing-type">${row.tenloaisp || ''}</div>
            <div class="listing-prop listing-button permission-xoa">
                <a href="javascript:void(0);" onclick="removeRow('${row.masp}', './product/ajax.php?action=delete')">Xóa</a>
            </div>
            <div class="listing-prop listing-button permission-sua">
                <a href="./product/${config_name}_edit.php?id=${row.masp}">Sửa</a>
            </div>
            <div class="listing-prop listing-button">
                ${row.soluong == 0 ? '<span style="color: red; font-weight: bold;">Đã bán hết</span>' : ''}
            </div>
            <div class="listing-prop listing-time">${new Date(row.created_time).toLocaleString()}</div>
            <div class="listing-prop listing-time">${new Date(row.last_updated).toLocaleString()}</div>
            <div class="clear-both"></div>
        `;
        productList.appendChild(li);
    });
}

function updatePagination(currentPage, totalPages) {
    $('#pagination').html('');
    for (let i = 1; i <= totalPages; i++) {
        const pageLink = `<a href="javascript:void(0);" onclick="loadProducts(${i});">${i}</a>`;
        if (i === currentPage) {
            $('#pagination').append(`<strong>${i}</strong>`);
        } else {
            $('#pagination').append(pageLink);
        }
    }
}

$(document).ready(function() {
    loadProducts(1);
});
</script>