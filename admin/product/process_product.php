<?php
include '../connect_db.php';
include 'function.php';

$db = new connect_db();

function validateProductInput($post) {
    if (empty($post['tensp'])) {
        return "Bạn phải nhập tên sản phẩm";
    }
    if (empty($post['dongiasanpham'])) {
        return "Bạn phải nhập giá sản phẩm";
    }
    if (!is_numeric(str_replace('.', '', $post['dongiasanpham']))) {
        return "Giá nhập không hợp lệ";
    }
    return null;
}

function handleImageUpload($db, $post, $files) {
    $image = null;
    if (isset($files['image']) && !empty($files['image']['name'])) {
        $result = uploadFiles($files['image']);
        if ($result === false || !isset($result['path'])) {
            return [null, "Không thể tải lên ảnh đại diện."];
        }
        return [$result['path'], null];
    } elseif (isset($post['masp']) && !empty($post['masp'])) {
        $product = $db->getById('product', $post['masp']);
        return [$product['image'], null];
    }
    return [null, null];
}

function handleGalleryUpload($files, $post) {
    $galleryImages = [];
    if (isset($files['gallery']) && !empty($files['gallery']['name'][0])) {
        $result = uploadFiles($files['gallery']);
        if (!empty($result['errors'])) {
            return [[], $result['errors']];
        }
        $galleryImages = $result['uploaded_files'];
    }
    if (isset($post['gallery_image']) && !empty($post['gallery_image'])) {
        $galleryImages = array_merge($galleryImages, $post['gallery_image']);
    }
    return [$galleryImages, null];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_product') {
    $error = validateProductInput($_POST);
    if ($error !== null) {
        echo json_encode(['status' => 'error', 'message' => $error]);
        exit;
    }

    list($image, $imageError) = handleImageUpload($db, $_POST, $_FILES);
    if ($imageError !== null) {
        echo json_encode(['status' => 'error', 'message' => $imageError]);
        exit;
    }
    // Nếu không có ảnh được chọn, gán ảnh mặc định
    if (empty($image)) {
        $image = 'uploads/default/OIP.jpg';
    }

    list($galleryImages, $galleryError) = handleGalleryUpload($_FILES, $_POST);
    if ($galleryError !== null) {
        echo json_encode(['status' => 'error', 'message' => $galleryError]);
        exit;
    }

    try {
        if (isset($_POST['masp']) && !empty($_POST['masp'])) {
            $db->update('product', [
                'tensp' => $_POST['tensp'],
                'soluong' => $_POST['soluong'],
                'image' => $image,
                'dongiasanpham' => str_replace('.', '', $_POST['dongiasanpham']),
                'content' => $_POST['content'],
                'maloaisp' => $_POST['maloaisp'],
                'last_updated' => time(),
                'masp' => $_POST['masp'],
                'mancc' => $_POST['mancc']
            ], $_POST['masp']);
        } else {
            $db->insert('product', [
                'tensp' => $_POST['tensp'],
                'soluong' => $_POST['soluong'],
                'image' => $image,
                'dongiasanpham' => str_replace('.', '', $_POST['dongiasanpham']),
                'content' => $_POST['content'],
                'maloaisp' => $_POST['maloaisp'],
                'mancc' => $_POST['mancc'],
                'created_time' => time(),
                'last_updated' => time()
            ]);
        }

        if (!empty($galleryImages)) {
            $productId = (isset($_POST['masp']) && !empty($_POST['masp'])) ? $_POST['masp'] : $db->getLastInsertId();
            foreach ($galleryImages as $path) {
                $db->insert('image_library', [
                    'product_id' => $productId,
                    'path' => $path,
                    'created_time' => time(),
                    'last_updated' => time()
                ]);
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Cập nhật thành công!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => "Có lỗi xảy ra: " . $e->getMessage()]);
    }
    exit;
}
?>
