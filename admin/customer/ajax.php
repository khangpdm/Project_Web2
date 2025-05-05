<?php
require_once __DIR__ . '/../../admin/connect_db.php';
$db = new connect_db();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'pagination':
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 5;
        $id = $_GET['id'] ?? '';
        $name = $_GET['name'] ?? '';

        $params = [];
        $where = " WHERE 1=1 ";
        if ($id !== '') {
            $where .= " AND c.macustomer LIKE :id ";
            $params['id'] = "%$id%";
        }
        if ($name !== '') {
            $where .= " AND c.name LIKE :name ";
            $params['name'] = "%$name%";
        }

        $totalQuery = "SELECT COUNT(*) FROM customer c $where";
        $totalStmt = $db->query($totalQuery, $params);
        $totalRecordsCount = $totalStmt->fetchColumn();

        $totalPages = ceil($totalRecordsCount / $per_page);
        $offset = ($page - 1) * $per_page;

        $query = "SELECT c.*, p.name AS province_name, d.name AS district_name 
                  FROM customer c 
                  LEFT JOIN provinces p ON c.province_id = p.province_id 
                  LEFT JOIN districts d ON c.district_id = d.district_id 
                  $where 
                  ORDER BY c.macustomer ASC 
                  LIMIT $offset, $per_page";
        $stmt = $db->query($query, $params);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'customers' => $customers,
            'totalRecordsCount' => $totalRecordsCount,
            'totalPages' => $totalPages,
        ]);
        break;

    case 'delete':
        $id = $_GET['id'] ?? '';
        if ($id === '') {
            echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
            exit;
        }
        $customer = $db->getById('customer', $id);
        if (!$customer) {
            echo json_encode(['success' => false, 'message' => 'Khách hàng không tồn tại']);
            exit;
        }
        $deleted = $db->delete('customer', $id);
        echo json_encode(['success' => $deleted]);
        break;

    case 'add':
        $data = json_decode(file_get_contents(filename: 'php://input'), true);
        if (!$data) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            exit;
        }
        $username = trim($data['username'] ?? '');
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $province_id = trim($data['province_id'] ?? '');
        $district_id = trim($data['district_id'] ?? '');
        $address_detail = trim($data['address_detail'] ?? '');
        $password = $data['password'] ?? '';

        if ($username === '' || $name === '' || $email === '' || $phone === '' || $password === '') {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc']);
            exit;
        }

        $existingCustomer = $db->query("SELECT macustomer FROM customer WHERE email = ?", [$email])->fetch();
        if ($existingCustomer) {
            echo json_encode(['success' => false, 'message' => 'Email đã tồn tại']);
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $customerData = [
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'province_id' => $province_id ?: null,
            'district_id' => $district_id ?: null,
            'address_detail' => $address_detail ?: null,
            'password' => $hashedPassword,
        ];

        $inserted = $db->insert('customer', $customerData);
        echo json_encode(['success' => (bool)$inserted]);
        break;

    case 'edit':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            exit;
        }
        $id = $data['id'] ?? '';
        $username = trim($data['username'] ?? '');
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $province_id = trim($data['province_id'] ?? '');
        $district_id = trim($data['district_id'] ?? '');
        $address_detail = trim($data['address_detail'] ?? '');
        $password = $data['password'] ?? '';

        if ($id === '' || $username === '' || $name === '' || $email === '' || $phone === '') {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc']);
            exit;
        }

        $customer = $db->getById('customer', $id);
        if (!$customer) {
            echo json_encode(['success' => false, 'message' => 'Khách hàng không tồn tại']);
            exit;
        }

        $updateData = [
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'province_id' => $province_id ?: null,
            'district_id' => $district_id ?: null,
            'address_detail' => $address_detail ?: null,
        ];

        if ($password !== '') {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $updated = $db->update('customer', $updateData, $id);
        echo json_encode(['success' => (bool)$updated]);
        break;

    case 'toggle_status':
        try {
            $id = $_POST['id'] ?? '';
            if ($id === '') {
                echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
                exit;
            }
            $customer = $db->getById('customer', $id);
            if (!$customer) {
                echo json_encode(['success' => false, 'message' => 'Khách hàng không tồn tại']);
                exit;
            }
            $newStatus = ($customer['status'] == 1) ? 0 : 1;
            $updated = $db->update('customer', ['status' => $newStatus], $id);
            if ($updated) {
                echo json_encode(['success' => true, 'newStatus' => $newStatus]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cập nhật trạng thái thất bại']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
        break;
}