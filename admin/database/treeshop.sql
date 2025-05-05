-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2025 at 10:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `treeshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `bill`
--

CREATE TABLE `bill` (
  `mabill` varchar(20) NOT NULL,
  `macustomer` varchar(20) NOT NULL,
  `maorder` varchar(20) NOT NULL,
  `mapayby` varchar(20) NOT NULL,
  `ngaymua` datetime(6) DEFAULT current_timestamp(6),
  `tongtien` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill`
--

INSERT INTO `bill` (`mabill`, `macustomer`, `maorder`, `mapayby`, `ngaymua`, `tongtien`) VALUES
('BIL001', 'CUS001', 'ORD001', 'PAY001', '2025-04-21 15:21:58.000000', 350000),
('BIL002', 'CUS002', 'ORD002', 'PAY002', '2025-04-21 15:21:58.000000', 200000);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `magiohang` varchar(20) NOT NULL,
  `mauser` varchar(20) NOT NULL,
  `maorder` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`magiohang`, `mauser`, `maorder`) VALUES
('CART001', 'CUS001', 'ORD001'),
('CART002', 'CUS002', 'ORD002');

--
-- Triggers `cart`
--
DELIMITER $$
CREATE TRIGGER `after_update_cart` AFTER UPDATE ON `cart` FOR EACH ROW BEGIN
    -- Kiểm tra nếu maorder được cập nhật từ NULL thành một giá trị hợp lệ
    IF OLD.maorder IS NULL AND NEW.maorder IS NOT NULL THEN
        -- Cập nhật số lượng sản phẩm trong bảng product
        UPDATE product p
        JOIN product_cart pc ON p.masp = pc.masp
        SET p.soluong = p.soluong - pc.soluong
        WHERE pc.magiohang = NEW.magiohang;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `macomment` int(11) NOT NULL,
  `noidung` text NOT NULL,
  `ngaydang` datetime(6) DEFAULT current_timestamp(6),
  `masp` varchar(20) NOT NULL,
  `mauser` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`macomment`, `noidung`, `ngaydang`, `masp`, `mauser`) VALUES
(1, 'Cây rất đẹp và dễ chăm sóc!', '2025-04-21 15:21:59.089893', 'PRO001', 'CUS001'),
(2, 'Giao hàng nhanh, cây tươi tốt.', '2025-04-21 15:21:59.089893', 'PRO002', 'CUS002');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `macustomer` varchar(20) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `name` varchar(25) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `province_id` varchar(10) DEFAULT NULL,
  `district_id` varchar(10) DEFAULT NULL,
  `address_detail` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`macustomer`, `username`, `password`, `phone`, `name`, `email`, `province_id`, `district_id`, `address_detail`) VALUES
('CUS001', 'nguyen_van_a', 'password_hash', '0123456789', 'Nguyễn Văn A', 'a@example.com', NULL, NULL, NULL),
('CUS002', 'tran_thi_b', 'password_hash', '0987654321', 'Trần Thị B', 'b@example.com', NULL, NULL, NULL),
('CUS003', 'nguyen_van_a', 'password_hash', '0123456789', 'Nguyễn Văn A', 'a@example.com', NULL, NULL, NULL),
('CUS004', 'tran_thi_b', 'password_hash', '0987654321', 'Trần Thị B', 'b@example.com', NULL, NULL, NULL),
('CUS005', 'nguyen_van_a', 'password_hash', '0123456789', 'Nguyễn Văn A', 'a@example.com', NULL, NULL, NULL),
('CUS006', 'tran_thi_b', 'password_hash', '0987654321', 'Trần Thị B', 'b@example.com', NULL, NULL, NULL),
('CUS007', 'nguyen_van_a', 'password_hash', '0123456789', 'Nguyễn Văn A', 'a@example.com', NULL, NULL, NULL),
('CUS008', 'tran_thi_b', 'password_hash', '0987654321', 'Trần Thị B', 'b@example.com', NULL, NULL, NULL),
('CUS009', 'nguyen_van_a', 'password_hash', '0123456789', 'Nguyễn Văn A', 'a@example.com', NULL, NULL, NULL),
('CUS010', 'tran_thi_b', 'password_hash', '0987654321', 'Trần Thị B', 'b@example.com', NULL, NULL, NULL),
('CUS011', 'nguyen_van_a', 'password_hash', '0123456789', 'Nguyễn Văn A', 'a@example.com', NULL, NULL, NULL),
('CUS012', 'tran_thi_b', 'password_hash', '0987654321', 'Trần Thị B', 'b@example.com', NULL, NULL, NULL),
('CUS013', 'nguyen_van_a', 'password_hash', '0123456789', 'Nguyễn Văn A', 'a@example.com', NULL, NULL, NULL),
('CUS014', 'tran_thi_b', 'password_hash', '0987654321', 'Trần Thị B', 'b@example.com', NULL, NULL, NULL),
('CUS015', 'nguyen_van_a', 'password_hash', '0123456789', 'Nguyễn Văn A', 'a@example.com', NULL, NULL, NULL),
('CUS016', 'tran_thi_b', 'password_hash', '0987654321', 'Trần Thị B', 'b@example.com', NULL, NULL, NULL),
('CUS017', 'nguyen_van_a', 'password_hash', '0123456789', 'Nguyễn Văn A', 'a@example.com', NULL, NULL, NULL),
('CUS018', 'tran_thi_b', 'password_hash', '0987654321', 'Trần Thị B', 'b@example.com', NULL, NULL, NULL);

--
-- Triggers `customer`
--
DELIMITER $$
CREATE TRIGGER `before_insert_customer` BEFORE INSERT ON `customer` FOR EACH ROW BEGIN
    DECLARE new_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(macustomer,4) AS UNSIGNED)),0) + 1 INTO new_id FROM customer;
    SET NEW.macustomer = CONCAT('CUS', LPAD(CAST(new_id AS CHAR),3,'0')); 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_entry_form`
--

CREATE TABLE `detail_entry_form` (
  `maphieunhap` varchar(20) NOT NULL,
  `masp` varchar(20) NOT NULL,
  `dongianhap` int(11) NOT NULL,
  `soluongnhap` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_entry_form`
--

INSERT INTO `detail_entry_form` (`maphieunhap`, `masp`, `dongianhap`, `soluongnhap`) VALUES
('EFO001', 'PRO001', 1000, 10),
('EFO002', 'PRO001', 1000, 10),
('EFO002', 'PRO031', 1000, 10);

--
-- Triggers `detail_entry_form`
--
DELIMITER $$
CREATE TRIGGER `after_insert_detail_entry_form` AFTER INSERT ON `detail_entry_form` FOR EACH ROW BEGIN
	DECLARE profit DECIMAL(5,2);

	SELECT loinhuan
    INTO profit
    FROM entry_form
    WHERE maphieunhap= NEW.maphieunhap;
    
    UPDATE product SET soluong = soluong + NEW.soluongnhap, dongiasanpham = ROUND(NEW.dongianhap * (1 + profit / 100), -2) where masp = NEW.masp;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `district_id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `province_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`district_id`, `name`, `province_id`) VALUES
('001', 'Quận 1', '01'),
('002', 'Quận 3', '01'),
('003', 'Quận 7', '01'),
('004', 'Quận 10', '01'),
('005', 'Quận 5', '01'),
('006', 'Huyện Bình Chánh', '01'),
('007', 'Huyện Nhà Bè', '01'),
('008', 'Huyện Cần Giờ', '01'),
('009', 'Quận Hải Châu', '02'),
('010', 'Quận Thanh Khê', '02'),
('011', 'Quận Liên Chiểu', '02'),
('012', 'Huyện Hòa Vang', '02'),
('013', 'Quận Ba Đình', '03'),
('014', 'Quận Hoàn Kiếm', '03'),
('015', 'Quận Đống Đa', '03'),
('016', 'Huyện Đông Anh', '03');

-- --------------------------------------------------------

--
-- Table structure for table `entry_form`
--

CREATE TABLE `entry_form` (
  `maphieunhap` varchar(20) NOT NULL,
  `ngaynhap` datetime(6) DEFAULT NULL,
  `mancc` varchar(20) NOT NULL,
  `mastaff` varchar(20) NOT NULL,
  `loinhuan` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `entry_form`
--

INSERT INTO `entry_form` (`maphieunhap`, `ngaynhap`, `mancc`, `mastaff`, `loinhuan`, `status`) VALUES
('EFO001', '2025-04-26 00:41:05.000000', 'SUP001', 'STAFF001', 0, 1),
('EFO002', '2025-04-26 00:41:16.000000', 'SUP001', 'STAFF001', 20, 1);

--
-- Triggers `entry_form`
--
DELIMITER $$
CREATE TRIGGER `after_update_entry_form` AFTER UPDATE ON `entry_form` FOR EACH ROW BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE masp_input INT;
    DECLARE soluong_input INT;
    DECLARE current_stock INT;
    DECLARE out_of_stock BOOLEAN DEFAULT FALSE;

    DECLARE cur CURSOR FOR
        SELECT masp, soluongnhap
        FROM detail_entry_form
        WHERE maphieunhap = NEW.maphieunhap;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Chỉ xử lý khi chuyển trạng thái từ 1 -> 0 (hủy phiếu)
    IF OLD.status = 1 AND NEW.status = 0 THEN
        OPEN cur;

        read_loop: LOOP
            FETCH cur INTO masp_input, soluong_input;
            IF done THEN
                LEAVE read_loop;
            END IF;

            -- Kiểm tra tồn kho hiện tại
            SELECT soluong INTO current_stock
            FROM product
            WHERE masp = masp_input;

            -- Nếu không đủ hàng thì đánh dấu lỗi
            IF current_stock < soluong_input THEN
                SET out_of_stock = TRUE;
            END IF;
        END LOOP;

        CLOSE cur;

        -- Nếu có lỗi tồn kho thì báo lỗi
        IF out_of_stock THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Không đủ hàng trong kho để hủy phiếu nhập.';
        ELSE
            -- Nếu đủ tồn kho, tiến hành trừ kho
            SET done = FALSE; -- Reset lại biến done để duyệt lại
            OPEN cur;

            read_loop_update:LOOP
                FETCH cur INTO masp_input, soluong_input;
                IF done THEN
                    LEAVE read_loop_update;
                END IF;

                UPDATE product
                SET soluong = soluong - soluong_input
                WHERE masp = masp_input;
            END LOOP;

            CLOSE cur;
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_entry_form` BEFORE INSERT ON `entry_form` FOR EACH ROW BEGIN
    DECLARE new_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(maphieunhap,4) AS UNSIGNED)),0) + 1 INTO new_id FROM entry_form;
    SET NEW.maphieunhap = CONCAT('EFO', LPAD(CAST(new_id AS CHAR),3,'0')); 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `func`
--

CREATE TABLE `func` (
  `funcid` varchar(20) NOT NULL,
  `funcname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `func`
--

INSERT INTO `func` (`funcid`, `funcname`) VALUES
('sua', 'Sửa'),
('them', 'Thêm'),
('xem', 'Xem'),
('xoa', 'Xóa');

-- --------------------------------------------------------

--
-- Table structure for table `image_library`
--

CREATE TABLE `image_library` (
  `id` int(11) NOT NULL,
  `product_id` varchar(20) NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_time` int(11) NOT NULL,
  `last_updated` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `maorder` varchar(20) NOT NULL,
  `magiohang` varchar(20) DEFAULT NULL,
  `mauser` varchar(20) NOT NULL,
  `mabill` varchar(20) DEFAULT NULL,
  `status` enum('0','1','2','3') DEFAULT '0',
  `status_change_timestamp` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`maorder`, `magiohang`, `mauser`, `mabill`, `status`, `status_change_timestamp`, `created_at`) VALUES
('ORD001', 'CART001', 'CUS001', 'BIL001', '1', '2025-04-21 15:46:49', '2025-04-21 15:21:58'),
('ORD002', 'CART002', 'CUS002', 'BIL002', '1', '2025-04-21 15:21:58', '2025-04-21 15:21:58');

-- --------------------------------------------------------

--
-- Table structure for table `payby`
--

CREATE TABLE `payby` (
  `mapayby` varchar(20) NOT NULL,
  `paybyname` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`details`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payby`
--

INSERT INTO `payby` (`mapayby`, `paybyname`, `address`, `details`) VALUES
('PAY001', 'Tiền mặt', 'Cửa hàng', '{}'),
('PAY002', 'Thẻ tín dụng', 'Cửa hàng', '{}');

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `permissionid` varchar(20) NOT NULL,
  `permissionname` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`permissionid`, `permissionname`) VALUES
('DANHMUC', 'Danh mục'),
('DONHANG', 'Đơn hàng'),
('PHANQUYEN', 'Phân quyền'),
('SANPHAM', 'sản phẩm'),
('TAIKHOAN', 'Quản lý tài khoản');

-- --------------------------------------------------------

--
-- Table structure for table `powergroup`
--

CREATE TABLE `powergroup` (
  `powergroupid` varchar(20) NOT NULL,
  `powergroupname` varchar(50) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `powergroup`
--

INSERT INTO `powergroup` (`powergroupid`, `powergroupname`, `status`, `created_time`, `last_updated`) VALUES
('GRP001', 'Quản trị viên', 1, '2025-04-28 12:48:39', '2025-05-02 05:46:18');

--
-- Triggers `powergroup`
--
DELIMITER $$
CREATE TRIGGER `before_insert_powergroup` BEFORE INSERT ON `powergroup` FOR EACH ROW BEGIN
    DECLARE new_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(powergroupid,4) AS UNSIGNED)), 0) + 1 
    INTO new_id 
    FROM powergroup;
    
    SET NEW.powergroupid = CONCAT('GRP', LPAD(CAST(new_id AS CHAR), 3, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `powergroup_func_permission`
--

CREATE TABLE `powergroup_func_permission` (
  `powergroupid` varchar(20) NOT NULL,
  `funcid` varchar(20) NOT NULL,
  `permissionid` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `powergroup_func_permission`
--

INSERT INTO `powergroup_func_permission` (`powergroupid`, `funcid`, `permissionid`) VALUES
('GRP001', 'sua', 'DANHMUC'),
('GRP001', 'them', 'DANHMUC'),
('GRP001', 'xem', 'DANHMUC'),
('GRP001', 'sua', 'DONHANG'),
('GRP001', 'them', 'DONHANG'),
('GRP001', 'sua', 'PHANQUYEN');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `masp` varchar(20) NOT NULL,
  `tensp` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `dongiasanpham` int(11) DEFAULT NULL,
  `soluong` int(11) DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `created_time` int(11) DEFAULT NULL,
  `last_updated` int(11) DEFAULT NULL,
  `maloaisp` varchar(20) DEFAULT NULL,
  `mancc` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`masp`, `tensp`, `image`, `dongiasanpham`, `soluong`, `content`, `created_time`, `last_updated`, `maloaisp`, `mancc`) VALUES
('PRO001', 'Cây Lưỡi Hổ', 'images/luoi_ho.jpg', 1200, 79, 'Cây lưỡi hổ dễ chăm sóc, thích hợp để bàn làm việc.', 1745223237, 1745223237, 'TYP001', 'SUP001'),
('PRO002', 'Cây Hoa Hồng', 'images/hoa_hong.jpg', 200000, 48, 'Cây hoa hồng đỏ tươi, thích hợp làm quà tặng.', 1745223237, 1745223237, 'TYP002', 'SUP002'),
('PRO003', 'Cây Bonsai Tùng', 'images/bonsai_tung.jpg', 500000, 15, 'Cây bonsai tùng mini, mang phong thủy tốt.', 1745223237, 1745223237, 'TYP003', 'SUP001'),
('PRO004', 'Cây Lưỡi Hổ', 'images/luoi_ho.jpg', 150000, 50, 'Cây lưỡi hổ dễ chăm sóc, thích hợp để bàn làm việc.', 1745223360, 1745223360, 'TYP001', 'SUP001'),
('PRO005', 'Cây Hoa Hồng', 'images/hoa_hong.jpg', 200000, 30, 'Cây hoa hồng đỏ tươi, thích hợp làm quà tặng.', 1745223360, 1745223360, 'TYP002', 'SUP002'),
('PRO006', 'Cây Bonsai Tùng', 'images/bonsai_tung.jpg', 500000, 15, 'Cây bonsai tùng mini, mang phong thủy tốt.', 1745223360, 1745223360, 'TYP003', 'SUP001'),
('PRO007', 'Cây Lưỡi Hổ', 'images/luoi_ho.jpg', 150000, 50, 'Cây lưỡi hổ dễ chăm sóc, thích hợp để bàn làm việc.', 1745223383, 1745223383, 'TYP001', 'SUP001'),
('PRO008', 'Cây Hoa Hồng', 'images/hoa_hong.jpg', 200000, 30, 'Cây hoa hồng đỏ tươi, thích hợp làm quà tặng.', 1745223383, 1745223383, 'TYP002', 'SUP002'),
('PRO009', 'Cây Bonsai Tùng', 'images/bonsai_tung.jpg', 500000, 15, 'Cây bonsai tùng mini, mang phong thủy tốt.', 1745223383, 1745223383, 'TYP003', 'SUP001'),
('PRO010', 'Cây Lưỡi Hổ', 'images/luoi_ho.jpg', 150000, 50, 'Cây lưỡi hổ dễ chăm sóc, thích hợp để bàn làm việc.', 1745223426, 1745223426, 'TYP001', 'SUP001'),
('PRO011', 'Cây Hoa Hồng', 'images/hoa_hong.jpg', 200000, 30, 'Cây hoa hồng đỏ tươi, thích hợp làm quà tặng.', 1745223426, 1745223426, 'TYP002', 'SUP002'),
('PRO012', 'Cây Bonsai Tùng', 'images/bonsai_tung.jpg', 500000, 15, 'Cây bonsai tùng mini, mang phong thủy tốt.', 1745223426, 1745223426, 'TYP003', 'SUP001'),
('PRO013', 'Cây Lưỡi Hổ', 'images/luoi_ho.jpg', 150000, 50, 'Cây lưỡi hổ dễ chăm sóc, thích hợp để bàn làm việc.', 1745223477, 1745223477, 'TYP001', 'SUP001'),
('PRO014', 'Cây Hoa Hồng', 'images/hoa_hong.jpg', 200000, 30, 'Cây hoa hồng đỏ tươi, thích hợp làm quà tặng.', 1745223477, 1745223477, 'TYP002', 'SUP002'),
('PRO015', 'Cây Bonsai Tùng', 'images/bonsai_tung.jpg', 500000, 15, 'Cây bonsai tùng mini, mang phong thủy tốt.', 1745223477, 1745223477, 'TYP003', 'SUP001'),
('PRO016', 'Cây Lưỡi Hổ', 'images/luoi_ho.jpg', 150000, 50, 'Cây lưỡi hổ dễ chăm sóc, thích hợp để bàn làm việc.', 1745223586, 1745223586, 'TYP001', 'SUP001'),
('PRO017', 'Cây Hoa Hồng', 'images/hoa_hong.jpg', 200000, 30, 'Cây hoa hồng đỏ tươi, thích hợp làm quà tặng.', 1745223586, 1745223586, 'TYP002', 'SUP002'),
('PRO018', 'Cây Bonsai Tùng', 'images/bonsai_tung.jpg', 500000, 15, 'Cây bonsai tùng mini, mang phong thủy tốt.', 1745223586, 1745223586, 'TYP003', 'SUP001'),
('PRO019', 'Cây Lưỡi Hổ', 'images/luoi_ho.jpg', 150000, 50, 'Cây lưỡi hổ dễ chăm sóc, thích hợp để bàn làm việc.', 1745223613, 1745223613, 'TYP001', 'SUP001'),
('PRO020', 'Cây Hoa Hồng', 'images/hoa_hong.jpg', 200000, 30, 'Cây hoa hồng đỏ tươi, thích hợp làm quà tặng.', 1745223613, 1745223613, 'TYP002', 'SUP002'),
('PRO021', 'Cây Bonsai Tùng', 'images/bonsai_tung.jpg', 500000, 15, 'Cây bonsai tùng mini, mang phong thủy tốt.', 1745223613, 1745223613, 'TYP003', 'SUP001'),
('PRO022', 'Cây Lưỡi Hổ', 'images/luoi_ho.jpg', 150000, 50, 'Cây lưỡi hổ dễ chăm sóc, thích hợp để bàn làm việc.', 1745223644, 1745223644, 'TYP001', 'SUP001'),
('PRO023', 'Cây Hoa Hồng', 'images/hoa_hong.jpg', 200000, 30, 'Cây hoa hồng đỏ tươi, thích hợp làm quà tặng.', 1745223644, 1745223644, 'TYP002', 'SUP002'),
('PRO024', 'Cây Bonsai Tùng', 'images/bonsai_tung.jpg', 500000, 15, 'Cây bonsai tùng mini, mang phong thủy tốt.', 1745223644, 1745223644, 'TYP003', 'SUP001'),
('PRO025', 'Cây Lưỡi Hổ', 'images/luoi_ho.jpg', 150000, 50, 'Cây lưỡi hổ dễ chăm sóc, thích hợp để bàn làm việc.', 1745223668, 1745223668, 'TYP001', 'SUP001'),
('PRO028', 'Cây Lưỡi Hổ', 'images/luoi_ho.jpg', 150000, 50, 'Cây lưỡi hổ dễ chăm sóc, thích hợp để bàn làm việc.', 1745223718, 1745223718, 'TYP001', 'SUP001'),
('PRO029', 'Cây Hoa Hồng', 'images/hoa_hong.jpg', 200000, 30, 'Cây hoa hồng đỏ tươi, thích hợp làm quà tặng.', 1745223718, 1745223718, 'TYP002', 'SUP002'),
('PRO030', 'Cây Bonsai Tùn', 'images/bonsai_tung.jpg', 500000, 0, 'Cây bonsai tùng mini, mang phong thủy tốt.', 1745223718, 2025, 'TYP003', 'SUP002'),
('PRO031', '1', NULL, 1200, 11, '1', 2025, 2025, 'TYP001', 'SUP001');

--
-- Triggers `product`
--
DELIMITER $$
CREATE TRIGGER `before_insert_product` BEFORE INSERT ON `product` FOR EACH ROW BEGIN
    DECLARE new_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(masp,4) AS UNSIGNED)),0) + 1 INTO new_id FROM product;
    SET NEW.masp = CONCAT('PRO', LPAD(CAST(new_id AS CHAR),3,'0')); 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `producttype`
--

CREATE TABLE `producttype` (
  `maloaisp` varchar(20) NOT NULL,
  `tenloaisp` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `producttype`
--

INSERT INTO `producttype` (`maloaisp`, `tenloaisp`) VALUES
('TYP001', 'Hoa'),
('TYP002', 'Cây ăn quả'),
('TYP003', 'Cây cảnh trong nhà'),
('TYP004', 'Cây hoa'),
('TYP005', 'Cây bonsai'),
('TYP006', 'Cây cảnh trong nhà'),
('TYP007', 'Cây hoa'),
('TYP008', 'Cây bonsai'),
('TYP009', 'Cây cảnh trong nhà'),
('TYP010', 'Cây hoa'),
('TYP011', 'Cây bonsai'),
('TYP012', 'Cây cảnh trong nhà'),
('TYP013', 'Cây hoa'),
('TYP014', 'Cây bonsai'),
('TYP015', 'Cây cảnh trong nhà'),
('TYP016', 'Cây hoa'),
('TYP017', 'Cây bonsai'),
('TYP018', 'Cây cảnh trong nhà'),
('TYP019', 'Cây hoa'),
('TYP020', 'Cây bonsai'),
('TYP021', 'Cây cảnh trong nhà'),
('TYP022', 'Cây hoa'),
('TYP023', 'Cây bonsai'),
('TYP024', 'Cây cảnh trong nhà'),
('TYP025', 'Cây hoa'),
('TYP026', 'Cây bonsai'),
('TYP027', 'Cây cảnh trong nhà'),
('TYP028', 'Cây hoa'),
('TYP029', 'Cây bonsai'),
('TYP030', 'Cây cảnh trong nhà'),
('TYP031', 'Cây hoa'),
('TYP032', 'Cây bonsai');

--
-- Triggers `producttype`
--
DELIMITER $$
CREATE TRIGGER `before_insert_producttype` BEFORE INSERT ON `producttype` FOR EACH ROW BEGIN
    DECLARE new_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(maloaisp,4) AS UNSIGNED)),0) + 1 INTO new_id FROM producttype;
    SET NEW.maloaisp = CONCAT('TYP', LPAD(CAST(new_id AS CHAR),3,'0')); 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_cart`
--

CREATE TABLE `product_cart` (
  `masp` varchar(20) NOT NULL,
  `magiohang` varchar(20) NOT NULL,
  `soluong` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_cart`
--

INSERT INTO `product_cart` (`masp`, `magiohang`, `soluong`) VALUES
('PRO001', 'CART001', 1),
('PRO002', 'CART002', 2);

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `province_id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`province_id`, `name`) VALUES
('01', 'TP. Hồ Chí Minh'),
('02', 'TP. Đà Nẵng'),
('03', 'TP. Hà Nội');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `mastaff` varchar(20) NOT NULL,
  `staffname` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `powergroupid` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`mastaff`, `staffname`, `password`, `address`, `powergroupid`, `email`) VALUES
('STAFF001', 'Quản trị viên', 'password_hash', 'Địa chỉ quản trị', 'GRP001', 'admin@example.com'),
('STAFF002', 'roy', '123', '123', 'GRP001', '123');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `mancc` varchar(20) NOT NULL,
  `tencc` varchar(25) NOT NULL,
  `diachi` varchar(100) NOT NULL,
  `dienthoai` varchar(11) NOT NULL,
  `sofax` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`mancc`, `tencc`, `diachi`, `dienthoai`, `sofax`) VALUES
('SUP001', 'Bao Company', '123/A', '1234567891', '12341231'),
('SUP002', 'Nhà cung cấp A', '123 Đường Cây', '0123456789', '0123456789'),
('SUP003', 'Nhà cung cấp B', '456 Đường Hoa', '0987654321', '0987654321'),
('SUP004', 'Nhà cung cấp A', '123 Đường Cây', '0123456789', '0123456789'),
('SUP005', 'Nhà cung cấp B', '456 Đường Hoa', '0987654321', '0987654321'),
('SUP006', 'Nhà cung cấp A', '123 Đường Cây', '0123456789', '0123456789'),
('SUP007', 'Nhà cung cấp B', '456 Đường Hoa', '0987654321', '0987654321'),
('SUP008', 'Nhà cung cấp A', '123 Đường Cây', '0123456789', '0123456789'),
('SUP009', 'Nhà cung cấp B', '456 Đường Hoa', '0987654321', '0987654321'),
('SUP010', 'Nhà cung cấp A', '123 Đường Cây', '0123456789', '0123456789'),
('SUP011', 'Nhà cung cấp B', '456 Đường Hoa', '0987654321', '0987654321'),
('SUP012', 'Nhà cung cấp A', '123 Đường Cây', '0123456789', '0123456789'),
('SUP013', 'Nhà cung cấp B', '456 Đường Hoa', '0987654321', '0987654321'),
('SUP014', 'Nhà cung cấp A', '123 Đường Cây', '0123456789', '0123456789'),
('SUP015', 'Nhà cung cấp B', '456 Đường Hoa', '0987654321', '0987654321'),
('SUP016', 'Nhà cung cấp A', '123 Đường Cây', '0123456789', '0123456789'),
('SUP017', 'Nhà cung cấp B', '456 Đường Hoa', '0987654321', '0987654321'),
('SUP018', 'Nhà cung cấp A', '123 Đường Cây', '0123456789', '0123456789'),
('SUP019', 'Nhà cung cấp B', '456 Đường Hoa', '0987654321', '0987654321'),
('SUP020', 'Nhà cung cấp A', '123 Đường Cây', '0123456789', '0123456789'),
('SUP021', 'Nhà cung cấp B', '456 Đường Hoa', '0987654321', '0987654321');

--
-- Triggers `supplier`
--
DELIMITER $$
CREATE TRIGGER `before_insert_supplier` BEFORE INSERT ON `supplier` FOR EACH ROW BEGIN
    DECLARE new_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(mancc,4) AS UNSIGNED)),0) + 1 INTO new_id FROM supplier;
    SET NEW.mancc = CONCAT('SUP', LPAD(CAST(new_id AS CHAR),3,'0')); 
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bill`
--
ALTER TABLE `bill`
  ADD PRIMARY KEY (`mabill`),
  ADD KEY `fk_bill_macustomer` (`macustomer`),
  ADD KEY `fk_bill_maorder` (`maorder`),
  ADD KEY `fk_bill_mapayby` (`mapayby`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`magiohang`),
  ADD KEY `fk_cart_mauser` (`mauser`),
  ADD KEY `fk_cart_maorder` (`maorder`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`macomment`),
  ADD KEY `fk_comment_masp` (`masp`),
  ADD KEY `fk_comment_mauser` (`mauser`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`macustomer`),
  ADD KEY `fk_customer_province` (`province_id`),
  ADD KEY `fk_customer_district` (`district_id`);

--
-- Indexes for table `detail_entry_form`
--
ALTER TABLE `detail_entry_form`
  ADD PRIMARY KEY (`maphieunhap`,`masp`),
  ADD KEY `fk_detail_entry_form_masp` (`masp`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`district_id`),
  ADD KEY `province_id` (`province_id`);

--
-- Indexes for table `entry_form`
--
ALTER TABLE `entry_form`
  ADD PRIMARY KEY (`maphieunhap`),
  ADD KEY `fk_entry_form_mancc` (`mancc`),
  ADD KEY `fk_entry_form_mastaff` (`mastaff`);

--
-- Indexes for table `func`
--
ALTER TABLE `func`
  ADD PRIMARY KEY (`funcid`);

--
-- Indexes for table `image_library`
--
ALTER TABLE `image_library`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_image_library_product_1` (`product_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`maorder`),
  ADD KEY `fk_order_cart` (`magiohang`),
  ADD KEY `fk_order_user` (`mauser`),
  ADD KEY `fk_order_bill` (`mabill`);

--
-- Indexes for table `payby`
--
ALTER TABLE `payby`
  ADD PRIMARY KEY (`mapayby`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`permissionid`);

--
-- Indexes for table `powergroup`
--
ALTER TABLE `powergroup`
  ADD PRIMARY KEY (`powergroupid`);

--
-- Indexes for table `powergroup_func_permission`
--
ALTER TABLE `powergroup_func_permission`
  ADD KEY `fk_powergroup_func_permission_powergroupid` (`powergroupid`),
  ADD KEY `fk_powergroup_func_permission_funcid` (`funcid`),
  ADD KEY `fk_powergroup_func_permission_permissionid` (`permissionid`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`masp`),
  ADD KEY `fk_product_maloaisp` (`maloaisp`),
  ADD KEY `fk_product_mancc` (`mancc`);

--
-- Indexes for table `producttype`
--
ALTER TABLE `producttype`
  ADD PRIMARY KEY (`maloaisp`);

--
-- Indexes for table `product_cart`
--
ALTER TABLE `product_cart`
  ADD PRIMARY KEY (`masp`,`magiohang`),
  ADD KEY `fk_product_cart_cart` (`magiohang`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`province_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`mastaff`),
  ADD KEY `fk_staff_powergroup` (`powergroupid`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`mancc`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `macomment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `image_library`
--
ALTER TABLE `image_library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `fk_bill_macustomer` FOREIGN KEY (`macustomer`) REFERENCES `customer` (`macustomer`),
  ADD CONSTRAINT `fk_bill_maorder` FOREIGN KEY (`maorder`) REFERENCES `order` (`maorder`),
  ADD CONSTRAINT `fk_bill_mapayby` FOREIGN KEY (`mapayby`) REFERENCES `payby` (`mapayby`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_maorder` FOREIGN KEY (`maorder`) REFERENCES `order` (`maorder`),
  ADD CONSTRAINT `fk_cart_mauser` FOREIGN KEY (`mauser`) REFERENCES `customer` (`macustomer`);

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `fk_comment_masp` FOREIGN KEY (`masp`) REFERENCES `product` (`masp`),
  ADD CONSTRAINT `fk_comment_mauser` FOREIGN KEY (`mauser`) REFERENCES `customer` (`macustomer`);

--
-- Constraints for table `detail_entry_form`
--
ALTER TABLE `detail_entry_form`
  ADD CONSTRAINT `fk_detail_entry_form_masp` FOREIGN KEY (`masp`) REFERENCES `product` (`masp`);

--
-- Constraints for table `districts`
--
ALTER TABLE `districts`
  ADD CONSTRAINT `districts_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`province_id`);

--
-- Constraints for table `entry_form`
--
ALTER TABLE `entry_form`
  ADD CONSTRAINT `fk_entry_form_mancc` FOREIGN KEY (`mancc`) REFERENCES `supplier` (`mancc`),
  ADD CONSTRAINT `fk_entry_form_mastaff` FOREIGN KEY (`mastaff`) REFERENCES `staff` (`mastaff`);

--
-- Constraints for table `image_library`
--
ALTER TABLE `image_library`
  ADD CONSTRAINT `fk_image_library_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`masp`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_image_library_product_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`masp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `fk_order_bill` FOREIGN KEY (`mabill`) REFERENCES `bill` (`mabill`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_cart` FOREIGN KEY (`magiohang`) REFERENCES `cart` (`magiohang`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`mauser`) REFERENCES `customer` (`macustomer`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `powergroup_func_permission`
--
ALTER TABLE `powergroup_func_permission`
  ADD CONSTRAINT `fk_powergroup_func_permission_funcid` FOREIGN KEY (`funcid`) REFERENCES `func` (`funcid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_powergroup_func_permission_permissionid` FOREIGN KEY (`permissionid`) REFERENCES `permission` (`permissionid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_powergroup_func_permission_powergroupid` FOREIGN KEY (`powergroupid`) REFERENCES `powergroup` (`powergroupid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_maloaisp` FOREIGN KEY (`maloaisp`) REFERENCES `producttype` (`maloaisp`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product_mancc` FOREIGN KEY (`mancc`) REFERENCES `supplier` (`mancc`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product_cart`
--
ALTER TABLE `product_cart`
  ADD CONSTRAINT `fk_product_cart_cart` FOREIGN KEY (`magiohang`) REFERENCES `cart` (`magiohang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product_cart_product` FOREIGN KEY (`masp`) REFERENCES `product` (`masp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_staff_powergroup` FOREIGN KEY (`powergroupid`) REFERENCES `powergroup` (`powergroupid`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
