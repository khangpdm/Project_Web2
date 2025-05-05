    <?php
        include("../database/database.php");
        header("Content-Type: application/json");

        $ItemPerPage = 10;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $page = max(1, $page);

        $offset = ($page -1) * $ItemPerPage;


        $conditions = [];

        // Tìm kiếm sản phẩm theo tên
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
        if (!empty($keyword)){
            $conditions[] = "tensp LIKE '%$keyword%'";
        }

        // Tìm kiếm sản phẩm theo loại
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        if (!empty($type) && $type != 'all'){
            $conditions[] = "maloaisp = '$type'";
        }

        // Tìm kiếm sản phẩm theo khoảng giá
        $minPrice = isset($_POST['min']) ? $_POST['min'] : '';
        $maxPrice = isset($_POST['max'])? $_POST['max'] : '';
        if (!empty($minPrice) && empty($maxPrice)){
            $conditions[] = "dongiasanpham >= '$minPrice'";
        }
        if (!empty($maxPrice) && empty($minPrice)){
            $conditions[] = "dongiasanpham <= '$maxPrice'";
        }
        if (!empty($minPrice) &&!empty($maxPrice)){
            $conditions[] = "dongiasanpham BETWEEN '$minPrice' AND '$maxPrice'";
        }


        $whereClause = !empty($conditions) ? "WHERE ". implode(" AND ", $conditions) : "";


        $totalQuery  = $conn->query("SELECT COUNT(*) as total FROM product $whereClause");
        $row = mysqli_fetch_assoc($totalQuery);
        $TotalPage = ($row['total'] > 0) ? ceil($row['total'] / $ItemPerPage) : 1;
        
        $sql = "SELECT * FROM product $whereClause LIMIT $offset, $ItemPerPage";
        $result = $conn->query($sql);

        if (!$result) {
            echo json_encode(["error" => "Query failed"]);
            exit;
        }

        $response = ["products" => []];

        while ($row = mysqli_fetch_assoc($result)) {
            $response["products"][] = [
                "id" => $row["masp"],
                "name" => $row["tensp"],
                "price" => $row["dongiasanpham"],
                "image" => $row["image"],
                "soluong" => $row["soluong"],
                "mota" => $row["content"],
            ];
        }
        $response["total"] = $TotalPage;
        $response["page"] = $page;
        
        $types = $conn->query("SELECT maloaisp, tenloaisp FROM producttype ORDER BY tenloaisp ASC");
        
        $response["header__menu__sub"] = [];
        while($row = mysqli_fetch_assoc($types)){
            $response["header__menu__sub"][]=[
                "typeid" => $row["maloaisp"],
                "type" => $row["tenloaisp"]];
        }
        
        echo json_encode($response);
        mysqli_close($conn);
    ?>