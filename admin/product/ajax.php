<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once __DIR__ . '/../../admin/connect_db.php';

$db = new connect_db();

function buildSearchWhereClause(&$searchParams) {
    $where = "";
    if (!empty($_GET['id'])) {
        $where .= (!empty($where) ? " AND " : " WHERE ") . "`masp` LIKE :id";
        $searchParams['id'] = "%" . $_GET['id'] . "%";
    }
    if (!empty($_GET['name'])) {
        $where .= (!empty($where) ? " AND " : " WHERE ") . "`tensp` LIKE :name";
        $searchParams['name'] = "%" . $_GET['name'] . "%";
    }
    return $where;
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'pagination') {
        $page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
        $item_per_page = isset($_GET['per_page']) && is_numeric($_GET['per_page']) && $_GET['per_page'] > 0 ? (int)$_GET['per_page'] : 5;

        $searchParams = [];
        $where = buildSearchWhereClause($searchParams);

        $offset = ($page - 1) * $item_per_page;

        try {
            $query = "SELECT p.*, p.tensp AS name, pt.tenloaisp, p.created_time, p.last_updated FROM `product` p LEFT JOIN producttype pt ON p.maloaisp = pt.maloaisp" . $where . " ORDER BY p.`masp` DESC LIMIT $offset, $item_per_page";
            $stmt = $db->query($query, $searchParams);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $totalQuery = "SELECT COUNT(*) FROM `product`" . $where;
            $totalStmt = $db->query($totalQuery, $searchParams);
            $totalRecordsCount = $totalStmt->fetchColumn();
            $totalPages = ceil($totalRecordsCount / $item_per_page);

            echo json_encode(['success' => true, 'products' => $products, 'totalPages' => $totalPages, 'totalRecordsCount' => $totalRecordsCount]);
        } catch (PDOException $e) {
            error_log("Lỗi PDO: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi PDO: ' . $e->getMessage()]);
        } catch (Exception $e) {
            error_log("Lỗi: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    } elseif ($action == 'add') {
        $data = $_POST;
        $db->insert('product', $data);
        echo json_encode(['success' => true, 'lastInsertId' => $db->getLastInsertId()]);
    } elseif ($action == 'update') {
        $id = $_POST['id'];
        $data = $_POST;
        unset($data['id']);
        $db->update('product', $data, $id);
        echo json_encode(['success' => true]);
    } elseif ($action == 'delete') {
        $id = $_GET['id'];
        $db->delete('product', $id);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>