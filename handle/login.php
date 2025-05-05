<?php 
include('../database/database.php');

function kiem_tra($input, $regex) {
    return preg_match($regex, $input);
}

$response = ["status" => "error", "errors" => []];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['mode'])) {
        $mode = $_POST['mode'];

        // Xử lý ĐĂNG KÝ (sign-up)
        if ($mode == 'sign-in') {
            $name = $_POST['name'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $phone_number = $_POST['phone_number'];

            // Kiểm tra username/email đã tồn tại chưa
            $check_sql = "SELECT * FROM customer WHERE username = ? OR email = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                if ($row['username'] == $username) {
                    $response["errors"]["username"] = "Username đã tồn tại!";
                }
                if ($row['email'] == $email) {
                    $response["errors"]["email"] = "Email đã được sử dụng!";
                }
            }

            // Kiểm tra mật khẩu
            if (!kiem_tra($password, "/^(?=.*[a-z])(?=.*[A-Z]).{8,}$/")) {
                $response["errors"]["password"] = "Mật khẩu ít nhất 8 ký tự, có chữ hoa và chữ thường!";
            }

            if (!kiem_tra($email,"/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/")){
                $response["errors"]["email"] = "Email không hợp lệ!";
            }

            if (!kiem_tra($phone_number,"/^\d{10}$/")){
                $response["errors"]["phone_number"] = "Số điện thoại không hợp lệ!";
            }

            // Nếu không có lỗi thì thêm vào database
            if (empty($response["errors"])) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_sql = "INSERT INTO customer (username, password, phone, name, email) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("sssss", $username, $hashed_password, $phone_number, $name, $email);
                
                if ($stmt->execute()) {
                    $response = ["status" => "success", "message" => "Đăng ký thành công!"];
                } else {
                    $response = ["status" => "error", "message" => "Lỗi: " . $stmt->error];
                }
            }
            $stmt->close();
        }

        // Xử lý ĐĂNG NHẬP (sign-in)
        elseif ($mode == 'sign-up') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $check_sql = "SELECT * FROM customer WHERE username = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $customer = $result->fetch_assoc();
                if (password_verify($password, $customer['password'])) {
                    $response = ["status" => "success", "message" => "Đăng nhập thành công!"];
                    session_start();
                    $_SESSION['macustomer'] = $customer['macustomer'];
                    $response['username'] = $customer['username'];
                } else {
                    $response["errors"]["password"] = "Mật khẩu không chính xác!";
                }
            } else {
                $response["errors"]["username"] = "Tài khoản không tồn tại!";
            }
            $stmt->close();
        } else {
            $response = ["status" => "error", "message" => "Invalid mode!"];
        }
    }
}

// Trả kết quả JSON
echo json_encode($response);
$conn->close();