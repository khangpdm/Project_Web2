<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Haven</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>

<body>
    <?php 
    include("login.php");
    include("layout/headerr.php"); ?>

    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'trangchu';

    switch ($page) {
        case 'trangchu':
            include('trangchu.php');
            break;
        case 'sanpham':
            include('layout/contentt.php');
            break;
        case 'chinhsach':
            include('chinhsach.php');
            break;
        case 'giohang':
            include('giohang.php');
            break;
        case 'checkout':
            include('checkout.php');
            break;

        default:
            echo "Trang không tồn tại";
            break;
    }
    ?>
    <?php include("layout/footerr.php");?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="js/scripts.js?v=<?php echo time(); ?>"></script>
</body>

</html>