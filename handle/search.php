<?php
include("../database/database.php");
header("Content-Type: application/json");

$keyword = $_POST['keyword'] ?? '';

$sql = "SELECT masp, tensp, dongiasanpham, image, soluong, content FROM product 
        WHERE tensp LIKE '%$keyword%'";

$result = $conn->query($sql);
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

echo json_encode($response);
mysqli_close($conn);
?>