<?php
include("../database/database.php");
session_start();

header('Content-Type: application/json');

if ($_POST['action'] === 'check') {
    if (isset($_SESSION['macustomer'])) {
        $macustomer = $_SESSION['macustomer'];
        $check_sql = "SELECT * FROM customer where macustomer = ?";
        $stmt = $conn->prepare($check_sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'SQL error: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param("s", $macustomer);
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'SQL execute error: ' . $stmt->error]);
            exit();
        }
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $customer = $result->fetch_assoc();
            $_SESSION['username'] = $customer['username'];
            echo json_encode(["loggedIn" => true, "username" => $customer['username']]);
        } else {
            echo json_encode(["loggedIn" => false]);
        }
        $stmt->close();
    } else {
        echo json_encode(["loggedIn" => false]);
    }
    exit();
} else if ($_POST['action'] === 'logout') {
    session_unset();
    session_destroy();
    echo json_encode(["loggedIn" => false]);
    exit();
}

$conn->close();
exit();
?>