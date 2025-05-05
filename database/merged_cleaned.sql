CREATE TABLE IF NOT EXISTS `bill` (
  `mabill` varchar(20) NOT NULL,
  `macustomer` varchar(20) NOT NULL,
  `maorder` varchar(20) NOT NULL,
  `mapayby` varchar(20) NOT NULL,
  `ngaymua` datetime(6) DEFAULT current_timestamp(6),
  `tongtien` int(11) NOT NULL,
  PRIMARY KEY (`mabill`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `cart` (
  `magiohang` varchar(20) NOT NULL,
  `mauser` varchar(20) NOT NULL,
  `maorder` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`magiohang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `comment` (
  `macomment` int(11) NOT NULL AUTO_INCREMENT,
  `noidung` text NOT NULL,
  `ngaydang` datetime(6) DEFAULT current_timestamp(6),
  `masp` varchar(20) NOT NULL,
  `mauser` varchar(20) NOT NULL,
  PRIMARY KEY (`macomment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `customer` (
  `macustomer` varchar(20) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `name` varchar(25) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `default_address_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`macustomer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `detail_entry_form` (
  `maphieunhap` varchar(20) NOT NULL,
  `masp` varchar(20) NOT NULL,
  `dongianhap` int(11) NOT NULL,
  `soluongnhap` int(11) NOT NULL,
  PRIMARY KEY (`maphieunhap`, `masp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `entry_form` (
  `maphieunhap` varchar(20) NOT NULL,
  `ngaynhap` datetime(6) DEFAULT NULL,
  `mancc` varchar(20) NOT NULL,
  `mastaff` varchar(20) NOT NULL,
  PRIMARY KEY (`maphieunhap`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `func` (
  `funcid` varchar(20) NOT NULL,
  `funcname` varchar(50) NOT NULL,
  PRIMARY KEY (`funcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `image_library` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_time` int(11) NOT NULL,
  `last_updated` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `order` (
  `maorder` varchar(20) NOT NULL,
  `magiohang` varchar(20) NOT NULL,
  `mauser` varchar(20) NOT NULL,
  `address_deli` varchar(20) DEFAULT NULL,
  `address_id` varchar(20) DEFAULT NULL,
  `mabill` varchar(20) DEFAULT NULL,
  `status` enum('0','1','2','3') DEFAULT '0',
  `status_change_timestamp` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`maorder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `payby` (
  `mapayby` varchar(20) NOT NULL,
  `paybyname` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `details` JSON NOT NULL,
  PRIMARY KEY (`mapayby`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `permission` (
  `permissionid` varchar(20) NOT NULL,
  `permissionname` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `powergroup` (
  `powergroupid` varchar(20) NOT NULL,
  `powergroupname` varchar(50) NOT NULL,
  PRIMARY KEY (`powergroupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `powergroup_func_permission` (
  `powergroupid` varchar(20) NOT NULL,
  `funcid` varchar(20) NOT NULL,
  `permissionid` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `product` (
  `masp` varchar(20) DEFAULT NULL,
  `tensp` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `dongiasanpham` int(11) DEFAULT NULL,
  `soluong` int(11) DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `created_time` int(11) DEFAULT NULL,
  `last_updated` int(11) DEFAULT NULL,
  `maloaisp` varchar(20) DEFAULT NULL,
  `mancc` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`masp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `producttype` (
  `maloaisp` varchar(20) NOT NULL,
  `tenloaisp` varchar(50) NOT NULL,
  PRIMARY KEY (`maloaisp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `product_cart` (
  `masp` varchar(20) NOT NULL,
  `magiohang` varchar(20) NOT NULL,
  `soluong` int(11) NOT NULL,
  PRIMARY KEY (`masp`, `magiohang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `staff` (
  `mastaff` varchar(20) NOT NULL,
  `staffname` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `powergroupid` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`mastaff`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `supplier` (
  `mancc` varchar(20) NOT NULL,
  `tencc` varchar(25) NOT NULL,
  `diachi` varchar(100) NOT NULL,
  `dienthoai` varchar(11) NOT NULL,
  `sofax` varchar(11) NOT NULL,
  PRIMARY KEY (`mancc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `address` (
  `address_id` varchar(20) NOT NULL,
  `macustomer` varchar(20) NOT NULL,
  `city` varchar(50) NOT NULL,
  `district` varchar(50) NOT NULL,
  `street_address` varchar(100) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `powergroup_func` (
  `powergroupid` varchar(20) NOT NULL,
  `funcid` varchar(20) NOT NULL,
  PRIMARY KEY (`powergroupid`, `funcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2025 at 11:03 AM
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
-- Database: `tree_shopping`
--

-- --------------------------------------------------------

--
-- Table structure for table `bill`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`macustomer`, `username`, `password`, `default_address_id`, `phone`, `name`, `email`) VALUES
('CUS001', 'roy', '$2y$10$oGWPsYCkoaIU464iq2pZmO.sW/5A8Zk.YzzCeBa67Q9RHH.vFEpM6', NULL, '1234567884', 'roy', 'quocbao96@gmail.com');

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
-- Table structure for table `entry_form`
--

--
-- Triggers `entry_form`
--
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

-- --------------------------------------------------------

--
-- Table structure for table `image_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

-- --------------------------------------------------------

--
-- Table structure for table `payby`
--

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

-- --------------------------------------------------------

--
-- Table structure for table `powergroup`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`masp`, `tensp`, `soluong`, `dongiasanpham`, `maloaisp`, `mancc`, `img`) VALUES
('PRO001', 'Cẩm Tú Cầu', 0, 0, 'TYP001', 'SUP001', 'img/camtucau.png');

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

--
-- Dumping data for table `producttype`
--

INSERT INTO `producttype` (`maloaisp`, `tenloaisp`) VALUES
('TYP001', 'Hoa'),
('TYP002', 'Cây ăn quả');

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

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`mancc`, `tencc`, `diachi`, `dienthoai`, `sofax`) VALUES
('SUP001', 'Bao Company', '123/A', '1234567891', '12341231');

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
  ADD KEY `fk_cart_mauser` (`mauser`);

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
  ADD PRIMARY KEY (`macustomer`);

--
-- Indexes for table `detail_entry_form`
--
ALTER TABLE `detail_entry_form`
  ADD PRIMARY KEY (`maphieunhap`,`masp`),
  ADD KEY `fk_detail_entry_form_masp` (`masp`);

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
  ADD KEY `masp` (`masp`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`maorder`),
  ADD KEY `fk_order_magiohang` (`magiohang`),
  ADD KEY `fk_order_mauser` (`mauser`),
  ADD KEY `fk_order_mabill` (`mabill`);

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
  ADD PRIMARY KEY (`powergroupid`,`funcid`,`permissionid`),
  ADD KEY `FK_powergroup_func_permission_func` (`funcid`),
  ADD KEY `FK_powergroup_func_permission_permission` (`permissionid`);

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
  ADD KEY `fk_product_cart_magiohang` (`magiohang`);

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
  MODIFY `macomment` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `fk_bill_macustomer` FOREIGN KEY (`macustomer`) REFERENCES `customer` (`macustomer`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bill_maorder` FOREIGN KEY (`maorder`) REFERENCES `order` (`maorder`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bill_mapayby` FOREIGN KEY (`mapayby`) REFERENCES `payby` (`mapayby`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_mauser` FOREIGN KEY (`mauser`) REFERENCES `customer` (`macustomer`) ON DELETE CASCADE;

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `fk_comment_masp` FOREIGN KEY (`masp`) REFERENCES `product` (`masp`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comment_mauser` FOREIGN KEY (`mauser`) REFERENCES `customer` (`macustomer`) ON DELETE CASCADE;

--
-- Constraints for table `detail_entry_form`
--
ALTER TABLE `detail_entry_form`
  ADD CONSTRAINT `fk_detail_entry_form_maphieunhap` FOREIGN KEY (`maphieunhap`) REFERENCES `entry_form` (`maphieunhap`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_entry_form_masp` FOREIGN KEY (`masp`) REFERENCES `product` (`masp`) ON DELETE CASCADE;

--
-- Constraints for table `entry_form`
--
ALTER TABLE `entry_form`
  ADD CONSTRAINT `fk_entry_form_mancc` FOREIGN KEY (`mancc`) REFERENCES `supplier` (`mancc`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_entry_form_mastaff` FOREIGN KEY (`mastaff`) REFERENCES `staff` (`mastaff`) ON DELETE CASCADE;

--
-- Constraints for table `image_library`
--
ALTER TABLE `image_library`
  ADD CONSTRAINT `image_library_ibfk_1` FOREIGN KEY (`masp`) REFERENCES `product` (`masp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `fk_order_mabill` FOREIGN KEY (`mabill`) REFERENCES `bill` (`mabill`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_order_magiohang` FOREIGN KEY (`magiohang`) REFERENCES `cart` (`magiohang`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_mauser` FOREIGN KEY (`mauser`) REFERENCES `customer` (`macustomer`) ON DELETE CASCADE;

--
-- Constraints for table `powergroup_func_permission`
--
ALTER TABLE `powergroup_func_permission`
  ADD CONSTRAINT `fk_powergroup_func_permission_func` FOREIGN KEY (`funcid`) REFERENCES `func` (`funcid`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_powergroup_func_permission_permission` FOREIGN KEY (`permissionid`) REFERENCES `permission` (`permissionid`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_powergroup_func_permission_powergroup` FOREIGN KEY (`powergroupid`) REFERENCES `powergroup` (`powergroupid`) ON DELETE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_maloaisp` FOREIGN KEY (`maloaisp`) REFERENCES `producttype` (`maloaisp`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_product_mancc` FOREIGN KEY (`mancc`) REFERENCES `supplier` (`mancc`) ON DELETE CASCADE;

--
-- Constraints for table `product_cart`
--
ALTER TABLE `product_cart`
  ADD CONSTRAINT `fk_product_cart_magiohang` FOREIGN KEY (`magiohang`) REFERENCES `cart` (`magiohang`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_product_cart_masp` FOREIGN KEY (`masp`) REFERENCES `product` (`masp`) ON DELETE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_staff_powergroup` FOREIGN KEY (`powergroupid`) REFERENCES `powergroup` (`powergroupid`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- Combined schema from treeshop.sql and sieuthimini.sql

-- Table: address (from treeshop.sql)
-- Table: bill (merged)
-- Table: cart (merged)
-- Table: comment (merged)
-- Table: customer (merged)
-- Table: detail_entry_form (merged)
-- Table: entry_form (merged)
-- Table: func (merged)
-- Table: image_library (from treeshop.sql)
-- Table: order (merged)
-- Table: payby (merged)
-- Table: powergroup (merged)
-- Table: powergroup_func (merged)
-- Table: product (merged)
-- Table: producttype (merged)
-- Table: product_cart (merged)
-- Table: staff (merged)
-- Table: supplier (merged)
-- Foreign key constraints (merged)
ALTER TABLE `customer`
  ADD CONSTRAINT `fk_customer_address` FOREIGN KEY (`default_address_id`) REFERENCES `address` (`address_id`);

ALTER TABLE `entry_form`
  ADD CONSTRAINT `fk_entry_form_mancc` FOREIGN KEY (`mancc`) REFERENCES `supplier`(`mancc`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_entry_form_mastaff` FOREIGN KEY (`mastaff`) REFERENCES `staff`(`mastaff`) ON DELETE CASCADE;

ALTER TABLE `staff`
  ADD CONSTRAINT `fk_staff_powergroup` FOREIGN KEY (`powergroupid`) REFERENCES `powergroup`(`powergroupid`) ON DELETE SET NULL;

ALTER TABLE `powergroup_func`
  ADD CONSTRAINT `fk_powergroup_func_powergroup` FOREIGN KEY (`powergroupid`) REFERENCES `powergroup`(`powergroupid`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_powergroup_func_func` FOREIGN KEY (`funcid`) REFERENCES `func`(`funcid`) ON DELETE CASCADE;

ALTER TABLE `detail_entry_form`
  ADD CONSTRAINT `fk_detail_entry_form_maphieunhap` FOREIGN KEY (`maphieunhap`) REFERENCES `entry_form`(`maphieunhap`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_entry_form_masp` FOREIGN KEY (`masp`) REFERENCES `product`(`masp`) ON DELETE CASCADE;

ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_maloaisp` FOREIGN KEY (`maloaisp`) REFERENCES `producttype`(`maloaisp`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_product_mancc` FOREIGN KEY (`mancc`) REFERENCES `supplier`(`mancc`) ON DELETE CASCADE;

ALTER TABLE `comment`
  ADD CONSTRAINT `fk_comment_masp` FOREIGN KEY (`masp`) REFERENCES `product`(`masp`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comment_mauser` FOREIGN KEY (`mauser`) REFERENCES `customer`(`macustomer`) ON DELETE CASCADE;

ALTER TABLE `product_cart`
  ADD CONSTRAINT `fk_product_cart_masp` FOREIGN KEY (`masp`) REFERENCES `product`(`masp`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_product_cart_magiohang` FOREIGN KEY (`magiohang`) REFERENCES `cart`(`magiohang`) ON DELETE CASCADE;

ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_mauser` FOREIGN KEY (`mauser`) REFERENCES `customer`(`macustomer`) ON DELETE CASCADE;

ALTER TABLE `order`
  ADD CONSTRAINT `fk_order_magiohang` FOREIGN KEY (`magiohang`) REFERENCES `cart`(`magiohang`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_mauser` FOREIGN KEY (`mauser`) REFERENCES `customer`(`macustomer`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_mabill` FOREIGN KEY (`mabill`) REFERENCES `bill`(`mabill`) ON DELETE SET NULL;

ALTER TABLE `bill`
  ADD CONSTRAINT `fk_bill_macustomer` FOREIGN KEY (`macustomer`) REFERENCES `customer`(`macustomer`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bill_maorder` FOREIGN KEY (`maorder`) REFERENCES `order`(`maorder`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bill_mapayby` FOREIGN KEY (`mapayby`) REFERENCES `payby`(`mapayby`) ON DELETE CASCADE;

-- Triggers from sieuthimini.sql
DELIMITER $$

CREATE TRIGGER before_insert_customer
BEFORE INSERT ON customer
FOR EACH ROW
BEGIN
    DECLARE new_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(macustomer,4) AS UNSIGNED)),0) + 1 INTO new_id FROM customer;
    SET NEW.macustomer = CONCAT('CUS', LPAD(CAST(new_id AS CHAR),3,'0')); 
END$$

CREATE TRIGGER before_insert_product
BEFORE INSERT ON product
FOR EACH ROW
BEGIN
    DECLARE new_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(masp,4) AS UNSIGNED)),0) + 1 INTO new_id FROM product;
    SET NEW.masp = CONCAT('PRO', LPAD(CAST(new_id AS CHAR),3,'0')); 
END$$

CREATE TRIGGER before_insert_producttype
BEFORE INSERT ON producttype
FOR EACH ROW
BEGIN
    DECLARE new_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(maloaisp,4) AS UNSIGNED)),0) + 1 INTO new_id FROM producttype;
    SET NEW.maloaisp = CONCAT('TYP', LPAD(CAST(new_id AS CHAR),3,'0')); 
END$$

CREATE TRIGGER before_insert_supplier
BEFORE INSERT ON supplier
FOR EACH ROW
BEGIN
    DECLARE new_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(mancc,4) AS UNSIGNED)),0) + 1 INTO new_id FROM supplier;
    SET NEW.mancc = CONCAT('SUP', LPAD(CAST(new_id AS CHAR),3,'0')); 
END$$

CREATE TRIGGER before_insert_entry_form
BEFORE INSERT ON entry_form
FOR EACH ROW
BEGIN
    DECLARE new_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(maphieunhap,4) AS UNSIGNED)),0) + 1 INTO new_id FROM entry_form;
    SET NEW.maphieunhap = CONCAT('EFO', LPAD(CAST(new_id AS CHAR),3,'0')); 
END$$

CREATE TRIGGER after_insert_detail_entry_form
AFTER INSERT ON detail_entry_form
FOR EACH ROW
BEGIN
    UPDATE product SET soluong = soluong + NEW.soluongnhap where masp = NEW.masp;
END$$

CREATE TRIGGER after_update_cart
AFTER UPDATE ON cart
FOR EACH ROW
BEGIN
    -- Kiểm tra nếu maorder được cập nhật từ NULL thành một giá trị hợp lệ
    IF OLD.maorder IS NULL AND NEW.maorder IS NOT NULL THEN
        -- Cập nhật số lượng sản phẩm trong bảng product
        UPDATE product p
        JOIN product_cart pc ON p.masp = pc.masp
        SET p.soluong = p.soluong - pc.soluong
        WHERE pc.magiohang = NEW.magiohang;
    END IF;
END$$
DELIMITER ;