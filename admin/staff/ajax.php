<?php
require_once '../connect_db.php';
$db = new connect_db();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $id = $_GET['id'] ?? '';
        $name = $_GET['name'] ?? '';

        $params = [];
        $where = " WHERE 1=1 ";
        if ($id !== '') {
            $where .= " AND s.mastaff LIKE :id ";
            $params['id'] = "%$id%";
        }
        if ($name !== '') {
            $where .= " AND s.staffname LIKE :name ";
            $params['name'] = "%$name%";
        }

        $query = "SELECT s.*, p.powergroupname 
                  FROM staff s 
                  LEFT JOIN powergroup p ON s.powergroupid = p.powergroupid 
                  $where 
                  ORDER BY s.mastaff ASC";
        $stmt = $db->query($query, $params);
        $staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'staffs' => $staffs,
        ]);
        break;

    case 'delete':
        $id = $_GET['id'] ?? '';
        if ($id === '') {
            echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
            exit;
        }
        $staff = $db->getById('staff', $id);
        if (!$staff) {
            echo json_encode(['success' => false, 'message' => 'Nhân viên không tồn tại']);
            exit;
        }
        $deleted = $db->delete('staff', $id);
        echo json_encode(['success' => $deleted]);
        break;

    case 'add':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            exit;
        }
        $staffname = trim($data['staffname'] ?? '');
        $email = trim($data['email'] ?? '');
        $address = trim($data['address'] ?? '');
        $powergroupid = trim($data['powergroupid'] ?? '');
        $password = $data['password'] ?? '';

        if ($staffname === '' || $email === '' || $password === '') {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc']);
            exit;
        }

        $existingStaff = $db->query("SELECT mastaff FROM staff WHERE email = ?", [$email])->fetch();
        if ($existingStaff) {
            echo json_encode(['success' => false, 'message' => 'Email đã tồn tại']);
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $staffData = [
            'staffname' => $staffname,
            'email' => $email,
            'address' => $address ?: null,
            'powergroupid' => $powergroupid ?: null,
            'password' => $hashedPassword,
        ];

        $inserted = $db->insert('staff', $staffData);
        echo json_encode(['success' => (bool)$inserted]);
        break;

    case 'edit':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            exit;
        }
        $id = $data['id'] ?? '';
        $staffname = trim($data['staffname'] ?? '');
        $email = trim($data['email'] ?? '');
        $address = trim($data['address'] ?? '');
        $powergroupid = trim($data['powergroupid'] ?? '');
        $password = $data['password'] ?? '';

        if ($id === '' || $staffname === '' || $email === '') {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc']);
            exit;
        }

        $staff = $db->getById('staff', $id);
        if (!$staff) {
            echo json_encode(['success' => false, 'message' => 'Nhân viên không tồn tại']);
            exit;
        }

        $updateData = [
            'staffname' => $staffname,
            'email' => $email,
            'address' => $address ?: null,
            'powergroupid' => $powergroupid ?: null,
        ];

        if ($password !== '') {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $updated = $db->update('staff', $updateData, $id);
        echo json_encode(['success' => (bool)$updated]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
        break;
}
