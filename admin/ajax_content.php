<?php
// ajax_content.php
// This file serves content for AJAX requests based on the "page" parameter

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'danhmuc':
        include __DIR__ . '/customer/top_customers.php';
        break;
    case 'sanpham':
        include __DIR__ . '/product/product_list.php';
        break;
    case 'donhang':
        include __DIR__ . '/order/order_list.php';
        break;
    case 'customer':
        include __DIR__ . '/customer/customer_listing.php';
        break;
    case 'phanquyen':
        include __DIR__ . '/phanquyen/phanquyen_customer.php';
        break;
    case 'staff':
        include __DIR__ . '/staff/staff_listing.php';
        break;
    default:
        // For system dashboard, include only the main content part
        include __DIR__ . '/admin/system_dashboard.php';
        break;
}
?>