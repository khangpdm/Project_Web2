<?php
require_once __DIR__ . '/../../admin/connect_db.php';
$db = new connect_db();
$productId = !empty($_GET['masp']) ? $_GET['masp'] : (!empty($_GET['id']) ? $_GET['id'] : null);
if ($productId !== null) {
    $product = $db->getById('product', $productId);
    if ($product === false) {
        echo "Không tìm thấy sản phẩm.";
        exit;
    }
}
?>
<div class="main-content">
    <h1><?= !empty($_GET['masp']) ? "Sửa sản phẩm" : "Thêm sản phẩm" ?></h1>
    <div id="content-box">
        <form id="editing-form" method="POST"
            action="<?= !empty($product) ? "?action=edit&masp=" . htmlspecialchars($productId) : "?action=add" ?>"
            enctype="multipart/form-data">
            <input type="submit" title="Lưu sản phẩm" value="Lưu" />
            <div class="clear-both"></div>
            <div class="wrap-field">
                <input type="hidden" name="masp"
                    value="<?= !empty($product) ? htmlspecialchars($product['masp']) : "" ?>" />
                <label>Loại sản phẩm: </label>
                <select name="maloaisp" required>
                    <?php 
                    $types = $db->query("SELECT * FROM producttype")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($types as $type) {
                        $selected = (!empty($product) && $product['maloaisp'] == $type['maloaisp']) ? 'selected' : '';
                        echo "<option value='".htmlspecialchars($type['maloaisp'])."' $selected>".htmlspecialchars($type['tenloaisp'])."</option>";
                    }
                    ?>
                </select>
                <div class="clear-both"></div>
            </div>
            <div class="wrap-field">
                <label>Nhà cung cấp: </label>
                <select name="mancc" required>
                    <?php
                    $suppliers = $db->query("SELECT * FROM supplier")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($suppliers as $supplier) {
                        $selected = (!empty($product) && $product['mancc'] == $supplier['mancc']) ? 'selected' : '';
                        echo "<option value='".htmlspecialchars($supplier['mancc'])."' $selected>".htmlspecialchars($supplier['tencc'])."</option>";
                    }
                    ?>
                </select>
                <div class="clear-both"></div>
            </div>
            <div class="wrap-field">
                <label>Tên sản phẩm: </label>
                <input type="text" name="tensp"
                    value="<?= !empty($product) ? htmlspecialchars($product['tensp']) : "" ?>" required />
                <div class="clear-both"></div>
            </div>
            <div class="wrap-field">
                <label>Giá sản phẩm: </label>
                <input type="text" name="dongiasanpham"
                    value="<?= !empty($product) ? number_format($product['dongiasanpham'], 0, ",", ".") : "" ?>"
                    required />
                <div class="clear-both"></div>
            </div>
            <div class="wrap-field">
                <label>Tồn kho: </label>
                <input type="text" name="soluong" value="<?= !empty($product) ? $product['soluong'] : "" ?>" required />
                <div class="clear-both"></div>
            </div>
            <div class="wrap-field">
                <label>Ảnh đại diện: </label>
                <div class="right-wrap-field">
                    <div id="main-image-container">
                        <?php if (!empty($product['image'])) { ?>
                        <img src="../<?= htmlspecialchars($product['image']) ?>" class="preview-image"
                            style="max-width: 200px; margin-bottom: 10px;" /><br />
                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($product['image']) ?>" />
                        <?php } ?>
                    </div>
                    <input type="file" name="image" accept="image/*" id="main-image-upload" />
                </div>
                <div class="clear-both"></div>
            </div>
            <div class="wrap-field">
                <label>Thư viện ảnh: </label>
                <div class="right-wrap-field">
                    <div id="gallery-container">
                        <?php if (!empty($product['gallery'])) { ?>
                        <ul id="existing-gallery" style="list-style: none; padding: 0;">
                            <?php foreach ($product['gallery'] as $image) { ?>
                            <li style="display: inline-block; margin-right: 10px; position: relative;">
                                <img src="../<?= htmlspecialchars($image['path']) ?>" class="preview-image"
                                    style="max-width: 100px; max-height: 100px;" />
                                <a href="gallery_delete.php?id=<?= $image['id'] ?>"
                                    style="position: absolute; top: 0; right: 0; background: red; color: white; padding: 2px 5px;">X</a>
                            </li>
                            <?php } ?>
                        </ul>
                        <?php } ?>
                    </div>
                    <input type="file" multiple name="gallery[]" accept="image/*" id="gallery-upload" />
                    <div id="new-gallery-preview" style="margin-top: 10px;"></div>
                </div>
                <div class="clear-both"></div>
            </div>
            <div class="wrap-field">
                <label>Nội dung: </label>
                <textarea name="content"
                    id="product-content"><?= !empty($product) ? htmlspecialchars($product['content']) : "" ?></textarea>
                <div class="clear-both"></div>
            </div>
        </form>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Main image preview with both existing and new
    $('#main-image-upload').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#main-image-container').html(
                    `<img src="${e.target.result}" class="preview-image" style="max-width: 200px; margin-bottom: 10px;"/><br/>`
                );
            }
            reader.readAsDataURL(file);
        }
    });

    // Gallery images preview - maintains existing and shows new
    $('#gallery-upload').change(function(e) {
        const files = e.target.files;
        if (files.length > 0) {
            let newPreviewHtml = '<ul style="list-style: none; padding: 0; margin-top: 10px;">';

            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    newPreviewHtml += `
                        <li style="display: inline-block; margin-right: 10px;">
                            <img src="${e.target.result}" class="preview-image" style="max-width: 100px; max-height: 100px;"/>
                        </li>`;

                    // Update preview after last image
                    if (Array.from(files).indexOf(file) === files.length - 1) {
                        newPreviewHtml += '</ul>';
                        $('#new-gallery-preview').html(newPreviewHtml);
                    }
                }
                reader.readAsDataURL(file);
            });
        }
    });

    $('#editing-form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'save_product');

        $.ajax({
            url: 'process_product.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'success') {
                    alert(result.message);
                    location.href = '../index.php?page=sanpham';
                } else {
                    alert(result.message);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra trong quá trình xử lý yêu cầu. Vui lòng thử lại.');
            }
        });
    });
});
</script>