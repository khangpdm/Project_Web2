<?php
require_once __DIR__ . '/../../admin/connect_db.php';
$db = new connect_db();
$top_customers = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Query to get top 5 customers by total purchases
    $sql = "SELECT c.macustomer as customer_id, c.name as customer_name, 
                   SUM(b.tongtien) as total_purchase
            FROM bill b
            JOIN customer c ON b.macustomer = c.macustomer
            WHERE b.ngaymua BETWEEN ? AND ?
            GROUP BY c.macustomer
            ORDER BY total_purchase DESC
            LIMIT 5";
    
    $sort_order = $_POST['sort_order'] ?? 'DESC';
    $sql = str_replace('DESC', $sort_order, $sql);
    $top_customers = $db->query($sql, [$start_date, $end_date])->fetchAll(PDO::FETCH_ASSOC);

    // Get orders for each customer
    foreach ($top_customers as &$customer) {
        $sql = "SELECT o.maorder as order_id, b.tongtien as amount, b.ngaymua as order_date
                FROM `order` o
                JOIN bill b ON o.mabill = b.mabill
                WHERE b.macustomer = ?
                AND b.ngaymua BETWEEN ? AND ?";
        
        $customer['orders'] = $db->query($sql, [
            $customer['customer_id'],
            $start_date,
            $end_date
        ])->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/top_customers.css">
    <title>Top Customers Statistics</title>
</head>
<body>
    <div class="main-content">
            <h1>Top 5 Customers</h1>
            <form method="POST" class="filter-form">
                <div class="form-group">
                    <label for="start_date">Từ ngày:</label>
                    <input type="date" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="end_date">Đến ngày:</label>
                    <input type="date" name="end_date" required>
                </div>
                <div class="form-group">
                    <label for="sort_order">Sắp xếp theo:</label>
                    <select name="sort_order" class="form-control">
                        <option value="DESC">Giảm dần (cao nhất trước)</option>
                        <option value="ASC">Tăng dần (thấp nhất trước)</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Thống kê</button>
            </form>

        <?php if (!empty($top_customers)): ?>
            <div class="customer-list-container">
                <div class="customer-list-header">
                    <div class="customer-id">Mã khách hàng</div>
                    <div class="customer-name">Tên khách hàng</div>
                    <div class="total-purchase">Tổng chi tiêu</div>
                </div>
                <?php foreach ($top_customers as $customer): ?>
                <div class="customer-item">
                    <div class="customer-id"><?= $customer['customer_id'] ?></div>
                    <div class="customer-name"><?= $customer['customer_name'] ?></div>
                    <div class="total-purchase"><?= number_format($customer['total_purchase'], 0, ',', '.') ?>đ</div>
                </div>
                <div class="customer-orders">
                    <div class="orders-title">Danh sách đơn hàng:</div>
                    <?php foreach ($customer['orders'] as $order): ?>
                    <div class="order-item">
                        <a href="../admin/order/order_details.php?id=<?= $order['order_id'] ?>" class="order-link">
                            Đơn #<?= $order['order_id'] ?> - <?= number_format($order['amount'], 0, ',', '.') ?>đ
                        </a>
                        <span class="order-date">(<?= date('d/m/Y', strtotime($order['order_date'])) ?>)</span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
