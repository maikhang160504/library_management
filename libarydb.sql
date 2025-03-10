-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: librarydb
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `chi_tiet_phieu_muon`
--

DROP TABLE IF EXISTS `chi_tiet_phieu_muon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chi_tiet_phieu_muon` (
  `ma_ctpm` int(11) NOT NULL AUTO_INCREMENT,
  `ma_phieu_muon` int(11) DEFAULT NULL,
  `ma_sach` int(11) DEFAULT NULL,
  `so_luong` int(11) NOT NULL CHECK (`so_luong` > 0),
  PRIMARY KEY (`ma_ctpm`),
  KEY `ma_phieu_muon` (`ma_phieu_muon`),
  KEY `ma_sach` (`ma_sach`),
  CONSTRAINT `chi_tiet_phieu_muon_ibfk_1` FOREIGN KEY (`ma_phieu_muon`) REFERENCES `phieu_muon` (`ma_phieu_muon`) ON DELETE CASCADE,
  CONSTRAINT `chi_tiet_phieu_muon_ibfk_2` FOREIGN KEY (`ma_sach`) REFERENCES `sach` (`ma_sach`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chi_tiet_phieu_muon`
--

LOCK TABLES `chi_tiet_phieu_muon` WRITE;
/*!40000 ALTER TABLE `chi_tiet_phieu_muon` DISABLE KEYS */;
INSERT INTO `chi_tiet_phieu_muon` VALUES (2,2,1,1),(4,3,1,1),(5,4,1,1),(6,5,1,1),(7,6,1,1),(8,7,1,1),(9,8,2,1),(10,9,1,1),(11,9,4,2),(12,9,3,1);
/*!40000 ALTER TABLE `chi_tiet_phieu_muon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_gia`
--

DROP TABLE IF EXISTS `doc_gia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doc_gia` (
  `ma_doc_gia` int(11) NOT NULL AUTO_INCREMENT,
  `ten_doc_gia` varchar(255) NOT NULL,
  `ngay_sinh` date NOT NULL,
  `so_dien_thoai` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`ma_doc_gia`),
  UNIQUE KEY `so_dien_thoai` (`so_dien_thoai`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_gia`
--

LOCK TABLES `doc_gia` WRITE;
/*!40000 ALTER TABLE `doc_gia` DISABLE KEYS */;
INSERT INTO `doc_gia` VALUES (1,'Mai Nhật Khang','2004-12-05','0362385725','2025-03-08 12:54:48','');
/*!40000 ALTER TABLE `doc_gia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phieu_muon`
--

DROP TABLE IF EXISTS `phieu_muon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phieu_muon` (
  `ma_phieu_muon` int(11) NOT NULL AUTO_INCREMENT,
  `ma_doc_gia` int(11) DEFAULT NULL,
  `ngay_muon` date NOT NULL,
  `ngay_tra` date NOT NULL,
  `trang_thai` enum('Đang mượn','Đã trả') NOT NULL DEFAULT 'Đang mượn',
  PRIMARY KEY (`ma_phieu_muon`),
  KEY `ma_doc_gia` (`ma_doc_gia`),
  CONSTRAINT `phieu_muon_ibfk_1` FOREIGN KEY (`ma_doc_gia`) REFERENCES `doc_gia` (`ma_doc_gia`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phieu_muon`
--

LOCK TABLES `phieu_muon` WRITE;
/*!40000 ALTER TABLE `phieu_muon` DISABLE KEYS */;
INSERT INTO `phieu_muon` VALUES (2,1,'2025-03-08','2025-03-15','Đã trả'),(3,1,'2025-03-09','2025-03-10','Đã trả'),(4,1,'2025-02-27','2025-02-27','Đã trả'),(5,1,'2025-02-27','2025-02-27','Đã trả'),(6,1,'2025-02-27','2025-02-27','Đã trả'),(7,1,'2025-03-09','2025-03-09','Đã trả'),(8,1,'2025-03-09','2025-03-09','Đã trả'),(9,1,'2025-03-10','2025-03-11','Đã trả');
/*!40000 ALTER TABLE `phieu_muon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phieu_tra`
--

DROP TABLE IF EXISTS `phieu_tra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phieu_tra` (
  `ma_phieu_tra` int(11) NOT NULL AUTO_INCREMENT,
  `ma_ctpm` int(11) DEFAULT NULL,
  `ngay_tra_sach` date NOT NULL,
  `tien_phat` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`ma_phieu_tra`),
  KEY `ma_ctpm` (`ma_ctpm`),
  CONSTRAINT `phieu_tra_ibfk_1` FOREIGN KEY (`ma_ctpm`) REFERENCES `chi_tiet_phieu_muon` (`ma_ctpm`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phieu_tra`
--

LOCK TABLES `phieu_tra` WRITE;
/*!40000 ALTER TABLE `phieu_tra` DISABLE KEYS */;
INSERT INTO `phieu_tra` VALUES (1,2,'2025-03-08',0.00),(2,3,'2025-03-09',0.00),(6,6,'2025-03-09',752000.00),(7,7,'2025-03-09',752000.00),(8,5,'2025-03-09',752000.00),(10,8,'2025-03-09',0.00),(13,9,'2025-03-09',0.00),(14,10,'2025-03-10',0.00),(15,11,'2025-03-10',0.00),(16,12,'2025-03-10',0.00);
/*!40000 ALTER TABLE `phieu_tra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sach`
--

DROP TABLE IF EXISTS `sach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sach` (
  `ma_sach` int(11) NOT NULL AUTO_INCREMENT,
  `ma_tac_gia` int(11) DEFAULT NULL,
  `ma_the_loai` int(11) DEFAULT NULL,
  `ten_sach` varchar(255) NOT NULL,
  `nam_xuat_ban` year(4) NOT NULL,
  `nha_xuat_ban` varchar(255) DEFAULT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ma_sach`),
  KEY `ma_tac_gia` (`ma_tac_gia`),
  KEY `ma_the_loai` (`ma_the_loai`),
  CONSTRAINT `sach_ibfk_1` FOREIGN KEY (`ma_tac_gia`) REFERENCES `tac_gia` (`ma_tac_gia`) ON DELETE CASCADE,
  CONSTRAINT `sach_ibfk_2` FOREIGN KEY (`ma_the_loai`) REFERENCES `the_loai` (`ma_the_loai`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sach`
--

LOCK TABLES `sach` WRITE;
/*!40000 ALTER TABLE `sach` DISABLE KEYS */;
INSERT INTO `sach` VALUES 
(1,1,1,'Cho Tôi Xin Một Vé Đi Tuổi Thơ',2008,'NXB Trẻ',18),
(2,2,2,'Harry Potter và Hòn Đá Phù Thủy',1997,'Bloomsbury',17),
(3,3,3,'Sapiens: Lược Sử Loài Người',2011,'HarperCollins',12),
(4,4,4,'Đắc Nhân Tâm',1936,'Simon & Schuster',20),
(5,5,5,'Doraemon Tập 1',1970,'Shogakukan',25),
(6,6,6,'Thám Tử Lừng Danh Conan Tập 1',1994,'Shogakukan',30),
(7,7,7,'Cha Giàu Cha Nghèo',1997,'Plata Publishing',18),
(8,8,8,'Giáo Trình Cấu Trúc Dữ Liệu và Giải Thuật',2010,'NXB Giáo Dục',10),
(9,9,9,'Project Hail Mary',2021,'Ballantine Books',9),
(10,10,10,'Tomorrow, and Tomorrow, and Tomorrow',2022,'Knopf',7),
(11,11,1,'Nhà Giả Kim',1988,'HarperTorch',20),
(12,12,10,'Norwegian Wood',1987,'Kodansha',15),
(13,13,6,'Mật Mã Da Vinci',2003,'Doubleday',18),
(14,14,9,'1984',1949,'Secker & Warburg',25),
(15,15,1,'Phía Tây Không Có Gì Lạ',1929,'Propyläen Verlag',12),
(16,16,3,'Lược Sử Thời Gian',1988,'Bantam Books',22),
(17,17,10,'Hoàng Tử Bé',1943,'Reynal & Hitchcock',30),
(18,18,4,'Think and Grow Rich',1937,'The Ralston Society',17),
(19,19,4,'Outliers: The Story of Success',2008,'Little, Brown and Company',14),
(20,20,4,'Atomic Habits',2018,'Avery',19);
/*!40000 ALTER TABLE `sach` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tac_gia`
--

DROP TABLE IF EXISTS `tac_gia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tac_gia` (
  `ma_tac_gia` int(11) NOT NULL AUTO_INCREMENT,
  `ten_tac_gia` varchar(255) NOT NULL,
  PRIMARY KEY (`ma_tac_gia`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tac_gia`
--

LOCK TABLES `tac_gia` WRITE;
/*!40000 ALTER TABLE `tac_gia` DISABLE KEYS */;
INSERT INTO `tac_gia` VALUES (1,'Nguyễn Nhật Ánh'),(2,'J.K. Rowling'),(3,'Yuval Noah Harari'),(4,'Dale Carnegie'),(5,'Fujiko F. Fujio'),(6,'Gosho Aoyama'),(7,'Robert T. Kiyosaki'),(8,'Mai Nhật Khang'),(9,'Robert C. Martin'),(10,'Gabrielle Zevin'),(11,'Paulo Coelho'),(12,'Haruki Murakami'),(13,'Dan Brown'),(14,'George Orwell'),(15,'Erich Maria Remarque'),(16,'Stephen Hawking'),(17,'Antoine de Saint-Exupéry'),(18,'Napoleon Hill'),(19,'Malcolm Gladwell'),(20,'James Clear');
/*!40000 ALTER TABLE `tac_gia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `the_loai`
--

DROP TABLE IF EXISTS `the_loai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `the_loai` (
  `ma_the_loai` int(11) NOT NULL AUTO_INCREMENT,
  `ten_the_loai` varchar(255) NOT NULL,
  PRIMARY KEY (`ma_the_loai`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `the_loai`
--

LOCK TABLES `the_loai` WRITE;
/*!40000 ALTER TABLE `the_loai` DISABLE KEYS */;
INSERT INTO `the_loai` VALUES (1,'Tiểu thuyết'),(2,'Khoa học viễn tưởng'),(3,'Tiểu thuyết'),(4,'Giả tưởng'),(5,'Lịch sử'),(6,'Kỹ năng sống'),(7,'Truyện tranh'),(8,'Trinh thám'),(9,'Kinh doanh'),(10,'Công nghệ'),(11,'Khoa học viễn tưởng'),(12,'Tiểu thuyết đương đại'),(13,'Tiểu thuyết'),(14,'Giả tưởng'),(15,'Lịch sử'),(16,'Kỹ năng sống'),(17,'Truyện tranh'),(18,'Trinh thám'),(19,'Kinh doanh'),(20,'Công nghệ'),(21,'Khoa học viễn tưởng'),(22,'Tiểu thuyết đương đại');
/*!40000 ALTER TABLE `the_loai` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-10 22:05:29

-- drop procedure if exists ThemSach;
DELIMITER $$

CREATE PROCEDURE ThemSach(
    IN tenSach VARCHAR(255),
    IN tenTacGia VARCHAR(255),
    IN tenTheLoai VARCHAR(255),
    IN namXuatBan INT,
    IN nhaXuatBan VARCHAR(255),
    IN soLuong INT
)
BEGIN
    DECLARE maTacGia INT;
    DECLARE maTheLoai INT;

    -- Kiểm tra và thêm tác giả nếu chưa có
    SELECT ma_tac_gia INTO maTacGia FROM tac_gia WHERE ten_tac_gia = tenTacGia;
    IF maTacGia IS NULL THEN
        INSERT INTO tac_gia (ten_tac_gia) VALUES (tenTacGia);
        SET maTacGia = LAST_INSERT_ID();
    END IF;

    -- Kiểm tra và thêm thể loại nếu chưa có
    SELECT ma_the_loai INTO maTheLoai FROM the_loai WHERE ten_the_loai = tenTheLoai;
    IF maTheLoai IS NULL THEN
        INSERT INTO the_loai (ten_the_loai) VALUES (tenTheLoai);
        SET maTheLoai = LAST_INSERT_ID();
    END IF;

    -- Thêm sách
    INSERT INTO sach (ten_sach, ma_tac_gia, ma_the_loai, nam_xuat_ban, nha_xuat_ban, so_luong)
    VALUES (tenSach, maTacGia, maTheLoai, namXuatBan, nhaXuatBan, soLuong);
END$$

DELIMITER ;

-----------
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetBorrowStats`(IN time_filter VARCHAR(20))
BEGIN
    -- Xóa bảng tạm nếu đã tồn tại
    DROP TEMPORARY TABLE IF EXISTS TempBorrowStats;
    
    -- Tạo bảng tạm để lưu kết quả
    CREATE TEMPORARY TABLE TempBorrowStats AS
    SELECT 
        b.ma_sach, 
        b.ten_sach, 
        a.ten_tac_gia, 
        t.ten_the_loai, 
        COUNT(pm.ma_phieu_muon) AS so_lan_muon 
    FROM phieu_muon pm
    JOIN chi_tiet_phieu_muon ctm ON pm.ma_phieu_muon = ctm.ma_phieu_muon
    JOIN sach b ON ctm.ma_sach = b.ma_sach
    JOIN tac_gia a ON b.ma_tac_gia = a.ma_tac_gia
    JOIN the_loai t ON b.ma_the_loai = t.ma_the_loai
    WHERE 
        (time_filter = 'today' AND DATE(pm.ngay_muon) = CURDATE()) OR
        (time_filter = 'this_week' AND YEARWEEK(pm.ngay_muon, 1) = YEARWEEK(CURDATE(), 1)) OR
        (time_filter = 'this_month' AND YEAR(pm.ngay_muon) = YEAR(CURDATE()) AND MONTH(pm.ngay_muon) = MONTH(CURDATE())) OR
        (time_filter = 'this_year' AND YEAR(pm.ngay_muon) = YEAR(CURDATE()))
    GROUP BY b.ma_sach, b.ten_sach, a.ten_tac_gia, t.ten_the_loai;
    
    -- Xuất kết quả ra ngoài
    SELECT * FROM TempBorrowStats;
    
    -- Xóa bảng tạm
    DROP TEMPORARY TABLE IF EXISTS TempBorrowStats;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `KiemTraSoLuongSach`(maSach INT) RETURNS int(11)
    DETERMINISTIC
BEGIN
    DECLARE soLuongCon INT;
    SELECT so_luong INTO soLuongCon FROM sach WHERE ma_sach = maSach;
    RETURN soLuongCon;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `LayPhieuMuonChuaTra`()
BEGIN
    SELECT * FROM phieu_muon WHERE trang_thai = 'Đang mượn';
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `SoLuongSachConLai`(maSach INT) RETURNS int(11)
    DETERMINISTIC
BEGIN
    DECLARE so_luong_con INT;
    SELECT so_luong INTO so_luong_con FROM sach WHERE ma_sach = maSach;
    RETURN so_luong_con;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `ThongKeDocGiaMuonNam`()
BEGIN
    SELECT COUNT(DISTINCT ma_doc_gia) AS SoDocGiaMuon FROM phieu_muon
    WHERE YEAR(ngay_muon) = YEAR(CURDATE());
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `ThongKeDocGiaMuonNhieuNhat`(IN startDate DATE, IN endDate DATE)
BEGIN
    SELECT dg.ma_doc_gia, dg.ten_doc_gia, COUNT(pm.ma_phieu_muon) AS so_luot_muon
    FROM phieu_muon pm
    JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
    WHERE pm.ngay_muon BETWEEN startDate AND endDate
    GROUP BY dg.ma_doc_gia
    ORDER BY so_luot_muon DESC
    LIMIT 10; -- Top 10 độc giả mượn nhiều nhất
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `ThongKeSachMuonNhieuNhat`(IN startDate DATE, IN endDate DATE)
BEGIN
    SELECT s.ma_sach, s.ten_sach, COUNT(ctpm.ma_ctpm) AS so_luot_muon
    FROM chi_tiet_phieu_muon ctpm
    JOIN sach s ON ctpm.ma_sach = s.ma_sach
JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
    WHERE pm.ngay_muon BETWEEN startDate AND endDate
    GROUP BY s.ma_sach
    ORDER BY so_luot_muon DESC
    LIMIT 10; -- Top 10 sách được mượn nhiều nhất
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `XuatBaoCaoMuonTra`(IN `thang` INT, IN `nam` INT)
BEGIN
    SELECT DISTINCT
        pm.ma_phieu_muon, 
        pm.ma_doc_gia, 
        ctpm.ma_sach,
        s.ma_sach,
        tg.ma_tac_gia,
        pm.ngay_muon, 
        pm.ngay_tra as ngay_tra_du_kien, 
        pt.ngay_tra_sach as ngay_tra_thuc_te,
        pm.trang_thai, 
        IFNULL(pt.tien_phat, 0) AS tien_phat,
        COUNT(ctpm.ma_sach) AS so_lan_muon
    FROM phieu_muon pm
    JOIN chi_tiet_phieu_muon ctpm ON pm.ma_phieu_muon = ctpm.ma_phieu_muon
    JOIN sach s ON ctpm.ma_sach = s.ma_sach
    JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
    LEFT JOIN phieu_tra pt ON pt.ma_ctpm = ctpm.ma_ctpm
    WHERE MONTH(pm.ngay_muon) = thang AND YEAR(pm.ngay_muon) = nam
    GROUP BY pm.ma_phieu_muon, pm.ma_doc_gia, ctpm.ma_sach, s.ten_sach, tg.ten_tac_gia, pm.ngay_muon, pm.ngay_tra, pm.trang_thai, pt.tien_phat;
    
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `get_top_genre_by_reader`(reader_id INT) RETURNS varchar(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci
    DETERMINISTIC
BEGIN
    DECLARE top_genre VARCHAR(255);

    SELECT tl.ten_the_loai 
    INTO top_genre
    FROM phieu_muon pm
    JOIN chi_tiet_phieu_muon ctpm ON pm.ma_phieu_muon = ctpm.ma_phieu_muon
    JOIN sach s ON ctpm.ma_sach = s.ma_sach
    JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
    WHERE pm.ma_doc_gia = reader_id AND YEAR(pm.ngay_muon) = YEAR(CURRENT_DATE)
    GROUP BY tl.ten_the_loai
    ORDER BY COUNT(pm.ma_phieu_muon) DESC
    LIMIT 1;

    RETURN top_genre;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `total_borrows`(`period_type` VARCHAR(10)) RETURNS int(11)
    DETERMINISTIC
BEGIN
    DECLARE total INT;
    
    IF period_type = 'today' THEN
        SELECT COUNT(*) INTO total FROM phieu_muon as pm join chi_tiet_phieu_muon as ctpm 
on ctpm.ma_phieu_muon = pm.ma_phieu_muon WHERE DATE(ngay_muon) = CURDATE();
    
    ELSEIF period_type = 'this_week' THEN
          SELECT COUNT(*) INTO total FROM phieu_muon as pm join chi_tiet_phieu_muon as ctpm 
on ctpm.ma_phieu_muon = pm.ma_phieu_muon  WHERE  YEARWEEK(pm.ngay_muon, 1) = YEARWEEK(CURDATE(), 1);
    
    ELSEIF period_type = 'this_month' THEN
        SELECT COUNT(*) INTO total FROM phieu_muon as pm join chi_tiet_phieu_muon as ctpm 
on ctpm.ma_phieu_muon = pm.ma_phieu_muon WHERE MONTH(ngay_muon) = MONTH(CURDATE()) AND YEAR(ngay_muon) = YEAR(CURDATE());
    
    ELSEIF period_type = 'this_year' THEN
        SELECT COUNT(*) INTO total FROM phieu_muon as pm join chi_tiet_phieu_muon as ctpm 
on ctpm.ma_phieu_muon = pm.ma_phieu_muon WHERE YEAR(ngay_muon) = YEAR(CURDATE());
    
DELIMITER $$

-- Cập nhật số lượng sách khi mượn
CREATE TRIGGER `CapNhatSoLuongKhiMuon` 
AFTER INSERT ON `chi_tiet_phieu_muon`
FOR EACH ROW 
BEGIN
    UPDATE sach 
    SET so_luong = so_luong - 1 
    WHERE ma_sach = NEW.ma_sach;
END$$

-- Cập nhật số lượng sách khi trả
CREATE TRIGGER `CapNhatSoLuongKhiTra` 
AFTER INSERT ON `phieu_tra`
FOR EACH ROW 
BEGIN
    DECLARE soLuongMuon INT;
    SELECT COUNT(*) INTO soLuongMuon FROM chi_tiet_phieu_muon WHERE ma_ctpm = NEW.ma_ctpm;
    
    UPDATE sach 
    SET so_luong = so_luong + soLuongMuon 
    WHERE ma_sach = (SELECT ma_sach FROM chi_tiet_phieu_muon WHERE ma_ctpm = NEW.ma_ctpm);
END$$

-- Kiểm tra số lượng trước khi mượn sách
CREATE TRIGGER `KiemTraSoLuongTruocKhiThem` 
BEFORE INSERT ON `chi_tiet_phieu_muon`
FOR EACH ROW 
BEGIN
    DECLARE soLuongCon INT;
    SET soLuongCon = (SELECT so_luong FROM sach WHERE ma_sach = NEW.ma_sach);
    
    IF soLuongCon < 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Số lượng sách không đủ!';
    END IF;
END$$

-- Tính tiền phạt khi trả sách muộn
CREATE TRIGGER `TinhTienPhat` 
BEFORE INSERT ON `phieu_tra`
FOR EACH ROW 
BEGIN
    DECLARE ngay_muon DATE;
    DECLARE ngay_tra_du_kien DATE;
    DECLARE so_ngay_tre INT;

    SELECT pm.ngay_muon, pm.ngay_tra 
    INTO ngay_muon, ngay_tra_du_kien
    FROM phieu_muon pm
    JOIN chi_tiet_phieu_muon ctpm ON pm.ma_phieu_muon = ctpm.ma_phieu_muon
    WHERE ctpm.ma_ctpm = NEW.ma_ctpm;

    SET so_ngay_tre = DATEDIFF(NEW.ngay_tra_sach, ngay_tra_du_kien);

    IF so_ngay_tre > 0 THEN
        SET NEW.tien_phat = so_ngay_tre * 10000;
    ELSE
        SET NEW.tien_phat = 0;
    END IF;
END$$

DELIMITER ;
