<?php

class connect_db {
    private $host = "localhost";
    private $db_name = "treeshop1";
    private $username = "root";
    private $password = "";
    private $conn;

    // Kết nối CSDL
    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            die("Lỗi kết nối: " . $exception->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    // Hàm chạy truy vấn (SELECT, INSERT, UPDATE, DELETE)
    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Lấy danh sách (SELECT * FROM)
    public function getAll($table) {
        $sql = "SELECT * FROM $table";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy 1 bản ghi theo ID
    public function getById($table, $id) {
        // Adjust primary key column name for specific tables
        $primaryKey = 'id';
        if ($table === 'product') {
            $primaryKey = 'masp';
        } elseif ($table === 'customer') {
            $primaryKey = 'macustomer';
        } elseif ($table === 'order') {
            $primaryKey = 'maorder';
        } elseif ($table === 'cart') {
            $primaryKey = 'magiohang';
        } elseif ($table === 'bill') {
            $primaryKey = 'mabill';
        } elseif ($table === 'supplier') {
            $primaryKey = 'mancc';
        } elseif ($table === 'staff') {
            $primaryKey = 'mastaff';
        } elseif ($table === 'producttype') {
            $primaryKey = 'maloaisp';
        } elseif ($table === 'entry_form') {
            $primaryKey = 'maphieunhap';
        }
        $sql = "SELECT * FROM $table WHERE $primaryKey = :id";
        return $this->query($sql, ['id' => $id])->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm dữ liệu
    public function insert($table, $data) {

        // Add created_time and last_updated only for tables that have these columns
        $tablesWithTimestamps = ['product', 'order', 'bill', 'supplier', 'staff', 'producttype', 'entry_form'];

        if (in_array($table, $tablesWithTimestamps)) {
            $data['created_time'] = date('Y-m-d H:i:s'); 
            $data['last_updated'] = date('Y-m-d H:i:s'); 
        }

        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        return $this->query($sql, $data);
    }

    // Cập nhật dữ liệu
    public function update($table, $data, $id) {

        // Add last_updated only for tables that have this column
        $tablesWithLastUpdated = ['product', 'order', 'bill', 'supplier', 'staff', 'producttype', 'entry_form'];

        if (in_array($table, $tablesWithLastUpdated)) {
            $data['last_updated'] = date('Y-m-d H:i:s');  
        }

        // Adjust primary key column name for specific tables
        $primaryKey = 'id';
        if ($table === 'product') {
            $primaryKey = 'masp';
        } elseif ($table === 'customer') {
            $primaryKey = 'macustomer';
        } elseif ($table === 'order') {
            $primaryKey = 'maorder';
        } elseif ($table === 'cart') {
            $primaryKey = 'magiohang';
        } elseif ($table === 'bill') {
            $primaryKey = 'mabill';
        } elseif ($table === 'supplier') {
            $primaryKey = 'mancc';
        } elseif ($table === 'staff') {
            $primaryKey = 'mastaff';
        } elseif ($table === 'producttype') {
            $primaryKey = 'maloaisp';
        } elseif ($table === 'entry_form') {
            $primaryKey = 'maphieunhap';
        }

        $setValues = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));
        $sql = "UPDATE $table SET $setValues WHERE $primaryKey = :id";
        $data['id'] = $id;
        return $this->query($sql, $data);
    }

    // Xóa dữ liệu
    public function delete($table, $id) {
        // Adjust primary key column name for specific tables
        $primaryKey = 'id';
        if ($table === 'product') {
            $primaryKey = 'masp';
        } elseif ($table === 'customer') {
            $primaryKey = 'macustomer';
        } elseif ($table === 'order') {
            $primaryKey = 'maorder';
        } elseif ($table === 'cart') {
            $primaryKey = 'magiohang';
        } elseif ($table === 'bill') {
            $primaryKey = 'mabill';
        } elseif ($table === 'supplier') {
            $primaryKey = 'mancc';
        } elseif ($table === 'staff') {
            $primaryKey = 'mastaff';
        } elseif ($table === 'producttype') {
            $primaryKey = 'maloaisp';
        } elseif ($table === 'entry_form') {
            $primaryKey = 'maphieunhap';
        }
        $sql = "DELETE FROM $table WHERE $primaryKey = :id";
        return $this->query($sql, ['id' => $id]);
    }

    public function getLastInsertId() {
        return $this->conn->lastInsertId();
    }

    // Order management functions
    public function getOrdersWithFilters($status = '', $start_date = '', $end_date = '', $city = '', $district = '') {
        error_log("Filtering orders with params: status=$status, start_date=$start_date, end_date=$end_date, city=$city, district=$district");
        
        $params = [];
        
        $joinProvince = (!empty($city)) ? "INNER JOIN" : "LEFT JOIN";
        $joinDistrict = (!empty($district)) ? "INNER JOIN" : "LEFT JOIN";
        
        $sql = "SELECT o.*, c.name as customer_name, 
                       c.address_detail,
                       p.name as province_name,
                       d.name as district_name,
                       CONCAT(c.address_detail, ', ', d.name, ', ', p.name) as address
                FROM `order` o
                JOIN customer c ON o.mauser = c.macustomer
                $joinProvince provinces p ON c.province_id = p.province_id
                $joinDistrict districts d ON c.district_id = d.district_id
                WHERE 1=1";
        
        if ($status !== '') {
            $sql .= " AND o.status = :status";
            $params['status'] = $status;
        }
        
        if (!empty($start_date)) {
            $sql .= " AND DATE(o.created_at) >= :start_date";
            $params['start_date'] = $start_date;
        }
        
        if (!empty($end_date)) {
            $sql .= " AND DATE(o.created_at) <= :end_date";
            $params['end_date'] = $end_date;
        }
        
        if (!empty($city)) {
            $sql .= " AND LOWER(p.name) LIKE LOWER(CONCAT('%', :city, '%'))";
            $params['city'] = $city;
        }
        
        if (!empty($district)) {
            $sql .= " AND LOWER(d.name) LIKE LOWER(CONCAT('%', :district, '%'))";
            $params['district'] = $district;
        }
        
        $sql .= " ORDER BY o.created_at DESC";
        
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderDetails($order_id) {
        // Get order info with bill details
        $sql = "SELECT o.*, c.name as customer_name, 
                       c.address_detail,
                       p.name as province_name,
                       d.name as district_name,
                       CONCAT(c.address_detail, ', ', d.name, ', ', p.name) as address,
                       c.phone, c.email,
                       b.mabill, b.tongtien, b.ngaymua,
                       pb.paybyname
                FROM `order` o
                JOIN customer c ON o.mauser = c.macustomer
                LEFT JOIN provinces p ON c.province_id = p.province_id
                LEFT JOIN districts d ON c.district_id = d.district_id
                LEFT JOIN bill b ON o.mabill = b.mabill
                LEFT JOIN payby pb ON b.mapayby = pb.mapayby
                WHERE o.maorder = :order_id";
        
        $order = $this->query($sql, ['order_id' => $order_id])->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) return false;
        
        // Get products and total from bill
        if (!empty($order['mabill'])) {
                    $sql = "SELECT p.tensp, pc.soluong, p.dongiasanpham
                           FROM product_cart pc
                JOIN product p ON pc.masp = p.masp
                JOIN cart c ON pc.magiohang = c.magiohang
                    JOIN `order` o ON c.maorder = o.maorder
                    WHERE o.mabill = :mabill";
            
            $items = $this->query($sql, ['mabill' => $order['mabill']])->fetchAll(PDO::FETCH_ASSOC);
            $order['items'] = $items;
            $order['total_amount'] = $order['tongtien'] ?? 0;
        } else {
            $order['items'] = [];
            $order['total_amount'] = 0;
        }
        
        return $order;
    }

    public function updateOrderStatus($order_id, $new_status) {
        $sql = "UPDATE `order` SET status = :status, status_change_timestamp = NOW() WHERE maorder = :order_id";
        return $this->query($sql, [
            'status' => $new_status,
            'order_id' => $order_id
        ]);
    }

    public function calculateOrderTotal($order_id) {
        $sql = "SELECT SUM(p.price * pc.soluong) as total
                FROM product_cart pc
                JOIN product p ON pc.masp = p.masp
                JOIN cart c ON pc.magiohang = c.magiohang
                JOIN `order` o ON c.maorder = o.maorder
                WHERE o.maorder = ?";
        $result = $this->query($sql, [$order_id])->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function createOrder($customer_id, $cart_id, $address_deli) {
        $order_id = 'ORD' . uniqid();
        $bill_id = 'BIL' . uniqid();
        
        // First create order with bill reference
        $this->query(
            "INSERT INTO `order` (maorder, magiohang, mauser, address_deli, mabill) 
             VALUES (?, ?, ?, ?, ?)",
            [$order_id, $cart_id, $customer_id, $address_deli, $bill_id]
        );
        
        // Calculate total from cart items
        $total = $this->calculateOrderTotal($order_id);
        
        // Then create linked bill
        $this->query(
            "INSERT INTO bill (mabill, macustomer, maorder, mapayby, ngaymua, tongtien)
             VALUES (?, ?, ?, 'CASH', NOW(), ?)",
            [$bill_id, $customer_id, $order_id, $total]
        );
        
        return $order_id;
    }

    public function getUniqueLocations() {
        $sql = "SELECT DISTINCT p.name as city, d.name as district
                FROM `order` o
                JOIN customer c ON o.mauser = c.macustomer
                LEFT JOIN provinces p ON c.province_id = p.province_id
                LEFT JOIN districts d ON c.district_id = d.district_id
                WHERE p.name IS NOT NULL AND d.name IS NOT NULL
                ORDER BY p.name, d.name";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getStatusText($status) {
        $statuses = [
            '0' => 'Chưa xác nhận',
            '1' => 'Đã xác nhận',
            '2' => 'Đã giao',
            '3' => 'Đã huỷ'
        ];
        return $statuses[$status] ?? 'Unknown';
    }
}
    
?>