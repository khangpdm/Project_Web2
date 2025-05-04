<?php
require_once __DIR__ . '/../connect_db.php';

$db = new connect_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_voucher'])) {
    $mancc = $_POST['mancc'] ?? '';
    $loinhuan = intval($_POST['loinhuan'] ?? 0);
    $mastaff = 'STAFF001'; // TODO: Replace with logged-in staff ID

    // Insert new entry_form record using connect_db insert method
    $data = [
        'mancc' => $mancc,
        'loinhuan' => $loinhuan,
        'mastaff' => $mastaff,
        'ngaynhap' => date('Y-m-d H:i:s')
    ];
    $result = $db->insert('entry_form', $data);

    if ($result) {
        $maphieunhap = $db->getLastInsertId();
        header("Location: entry_form.php?maphieunhap=" . $maphieunhap);
        exit;
    } else {
        $error = "Lỗi khi tạo phiếu nhập.";
    }
}

// Load suppliers
$suppliers = $db->getAll('supplier');

// If editing existing voucher, load details
$maphieunhap = $_GET['maphieunhap'] ?? '';
$voucher = null;
$details = [];
$total = 0;

if ($maphieunhap) {
    $voucher = $db->getById('entry_form', $maphieunhap);

    // Load detail products
    $sql = "SELECT d.*, p.tensp FROM detail_entry_form d JOIN product p ON d.masp = p.masp WHERE d.maphieunhap = ?";
    $stmt = $db->query($sql, [$maphieunhap]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $details[] = $row;
        $total += $row['dongianhap'] * $row['soluongnhap'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Phiếu Nhập</title>
    <link rel="stylesheet" href="../admin/css/order_styles.css">
</head>
<body>
    <h1>Quản lý Phiếu Nhập</h1>

    <?php if (!isset($voucher)): ?>
    <form method="POST" action="entry_form.php">
        <label for="mancc">Chọn Nhà Cung Cấp:</label>
        <select name="mancc" id="mancc" required>
            <option value="">-- Chọn nhà cung cấp --</option>
            <?php foreach ($suppliers as $supplier): ?>
                <option value="<?= htmlspecialchars($supplier['mancc']) ?>"><?= htmlspecialchars($supplier['tencc']) ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label for="loinhuan">Lợi Nhuận (%):</label>
        <input type="number" name="loinhuan" id="loinhuan" min="0" max="100" value="0" required>
        <br>
        <button type="submit" name="create_voucher">Tạo Phiếu Nhập</button>
    </form>
    <?php else: ?>
    <h2>Phiếu Nhập: <?= htmlspecialchars($voucher['maphieunhap']) ?></h2>
    <p>Nhà cung cấp: <?= htmlspecialchars($voucher['mancc']) ?></p>
    <p>Lợi nhuận: <?= htmlspecialchars($voucher['loinhuan']) ?>%</p>

    <h3>Thêm sản phẩm</h3>
    <form method="POST" action="add_product.php?maphieunhap=<?= urlencode($voucher['maphieunhap']) ?>">
        <label for="masp">Chọn sản phẩm:</label>
        <select name="masp" id="masp" required>
            <option value="">-- Chọn sản phẩm --</option>
            <?php
            $stmt = $db->query("SELECT masp, tensp FROM product WHERE mancc = ?", [$voucher['mancc']]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($products as $product):
            ?>
                <option value="<?= htmlspecialchars($product['masp']) ?>"><?= htmlspecialchars($product['tensp']) ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label for="dongianhap">Đơn giá nhập:</label>
        <input type="number" name="dongianhap" id="dongianhap" min="0" required>
        <br>
        <label for="soluongnhap">Số lượng nhập:</label>
        <input type="number" name="soluongnhap" id="soluongnhap" min="1" required>
        <br>
        <button type="submit" name="add_product">Thêm sản phẩm</button>
    </form>

    <h3>Chi tiết sản phẩm</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Mã sản phẩm</th>
                <th>Tên sản phẩm</th>
                <th>Đơn giá nhập</th>
                <th>Số lượng nhập</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['masp']) ?></td>
                <td><?= htmlspecialchars($item['tensp']) ?></td>
                <td><?= number_format($item['dongianhap']) ?></td>
                <td><?= $item['soluongnhap'] ?></td>
                <td><?= number_format($item['dongianhap'] * $item['soluongnhap']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Tổng tiền phiếu nhập: <?= number_format($total) ?></h3>

    <?php endif; ?>
</body>
</html>
