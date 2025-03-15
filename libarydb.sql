-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 15, 2025 lúc 09:37 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `librarydb`
--

DELIMITER $$
--
-- Thủ tục
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CapNhat` (IN `p_ma_sach` INT, IN `p_ten_sach` VARCHAR(255), IN `p_nam_xuat_ban` YEAR, IN `p_nha_xuat_ban` VARCHAR(255), IN `p_so_luong` INT, IN `p_ten_tac_gia` VARCHAR(255), IN `p_ten_the_loai` VARCHAR(255))   BEGIN
    DECLARE v_ma_tac_gia INT;
    DECLARE v_ma_the_loai INT;

    SELECT ma_tac_gia INTO v_ma_tac_gia
    FROM tac_gia
    WHERE ten_tac_gia = p_ten_tac_gia
    LIMIT 1;

    IF v_ma_tac_gia IS NULL THEN
        INSERT INTO tac_gia (ten_tac_gia) VALUES (p_ten_tac_gia);
        SET v_ma_tac_gia = LAST_INSERT_ID();
    END IF;

    SELECT ma_the_loai INTO v_ma_the_loai
    FROM the_loai
    WHERE ten_the_loai = p_ten_the_loai
    LIMIT 1;

    IF v_ma_the_loai IS NULL THEN
        INSERT INTO the_loai (ten_the_loai) VALUES (p_ten_the_loai);
        SET v_ma_the_loai = LAST_INSERT_ID();
    END IF;

    UPDATE sach
    SET ten_sach = p_ten_sach,
        nam_xuat_ban = p_nam_xuat_ban,
        nha_xuat_ban = p_nha_xuat_ban,
        so_luong = p_so_luong,
        ma_tac_gia = v_ma_tac_gia,
        ma_the_loai = v_ma_the_loai
    WHERE ma_sach = p_ma_sach;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CapNhatDocGia` (IN `p_ma_doc_gia` INT, IN `p_ten_doc_gia` VARCHAR(255), IN `p_ngay_sinh` DATE, IN `p_so_dien_thoai` VARCHAR(15))   BEGIN
    -- Cập nhật thông tin độc giả
    UPDATE doc_gia 
    SET ten_doc_gia = p_ten_doc_gia, 
        ngay_sinh = p_ngay_sinh, 
        so_dien_thoai = p_so_dien_thoai
    WHERE ma_doc_gia = p_ma_doc_gia;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CheckPenalty` (IN `p_ma_phieu_muon` INT, OUT `p_has_penalty` BOOLEAN)   BEGIN
    DECLARE v_tien_phat DECIMAL(10,2);
    -- Lấy số tiền phạt của phiếu mượn
    SELECT pt.tien_phat INTO v_tien_phat
    FROM phieu_muon pm 
    join chi_tiet_phieu_muon ctpm on ctpm.ma_phieu_muon = pm.ma_phieu_muon
    join phieu_tra pt on pt.ma_ctpm = ctpm.ma_ctpm
    WHERE pm.ma_phieu_muon = p_ma_phieu_muon;
    IF v_tien_phat > 0 THEN
        SET p_has_penalty = TRUE;
    ELSE
        SET p_has_penalty = FALSE;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetBorrowStats` (IN `time_filter` VARCHAR(20))   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUpcomingReturns` (IN `days` INT)   BEGIN
    -- Kiểm tra tham số đầu vào
    IF days <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tham số "days" phải lớn hơn 0';
    END IF;

    -- Truy vấn chính
    SELECT 
        pm.ma_phieu_muon, 
        pm.ma_doc_gia, 
        dg.ten_doc_gia, 
        s.ma_sach, 
        s.ten_sach, 
        pm.ngay_muon, 
        pm.ngay_tra AS ngay_tra_du_kien,
        pm.trang_thai
    FROM phieu_muon pm
    JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
    JOIN chi_tiet_phieu_muon ctpm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
    JOIN sach s ON ctpm.ma_sach = s.ma_sach
    WHERE pm.ngay_tra BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL days DAY)
      AND pm.trang_thai = 'Đang Mượn'
    ORDER BY pm.ngay_tra ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `LayPhieuMuonChuaTra` ()   BEGIN
    SELECT * FROM phieu_muon pm
    join doc_gia dg on dg.ma_doc_gia = pm.ma_doc_gia
    WHERE pm.trang_thai = 'Đang mượn';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `LayPhieuMuonDaTra` ()   BEGIN
    SELECT * FROM phieu_muon pm
    join doc_gia dg on dg.ma_doc_gia = pm.ma_doc_gia
    WHERE pm.trang_thai = 'Đã trả';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ThemDocGia` (IN `ten_doc_gia` VARCHAR(255), IN `ngay_sinh` DATE, IN `so_dien_thoai` VARCHAR(15))   BEGIN
    INSERT INTO doc_gia (ten_doc_gia, ngay_sinh, so_dien_thoai)
    VALUES (ten_doc_gia, ngay_sinh, so_dien_thoai);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ThemSach` (IN `p_tenSach` VARCHAR(255), IN `p_tenTacGia` VARCHAR(255), IN `p_tenTheLoai` VARCHAR(255), IN `p_namXuatBan` INT, IN `p_nhaXuatBan` VARCHAR(255), IN `p_soLuong` INT, IN `p_ngayThem` DATE)   BEGIN
    DECLARE v_maTacGia INT;
    DECLARE v_maTheLoai INT;

    -- Kiểm tra tác giả đã tồn tại chưa
    SELECT ma_tac_gia INTO v_maTacGia
    FROM tac_gia
    WHERE ten_tac_gia = p_tenTacGia
    LIMIT 1;

    IF v_maTacGia IS NULL THEN
        INSERT INTO tac_gia (ten_tac_gia) VALUES (p_tenTacGia);
        SET v_maTacGia = LAST_INSERT_ID();
    END IF;

    -- Kiểm tra thể loại đã tồn tại chưa
    SELECT ma_the_loai INTO v_maTheLoai
    FROM the_loai
    WHERE ten_the_loai = p_tenTheLoai
    LIMIT 1;

    IF v_maTheLoai IS NULL THEN
        INSERT INTO the_loai (ten_the_loai) VALUES (p_tenTheLoai);
        SET v_maTheLoai = LAST_INSERT_ID();
    END IF;

    -- Thêm sách mới, có cột ngay_them
    INSERT INTO sach (
        ma_tac_gia,
        ma_the_loai,
        ten_sach,
        nam_xuat_ban,
        nha_xuat_ban,
        so_luong,
        ngay_them
    ) VALUES (
        v_maTacGia,
        v_maTheLoai,
        p_tenSach,
        p_namXuatBan,
        p_nhaXuatBan,
        p_soLuong,
        p_ngayThem
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ThongKeChiTietTheoNam` ()   BEGIN
    SELECT 
        s.ma_sach,
        s.ten_sach,
        tg.ten_tac_gia,
        tl.ten_the_loai,
        s.so_luong,
        YEAR(s.ngay_them) AS period
    FROM sach s
    INNER JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
    INNER JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
    ORDER BY period DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ThongKeChiTietTheoNgay` ()   BEGIN
    SELECT 
        s.ma_sach,
        s.ten_sach,
        tg.ten_tac_gia,
        tl.ten_the_loai,
        s.so_luong,
        DATE(s.ngay_them) AS period
    FROM sach s
    INNER JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
    INNER JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
    ORDER BY s.ngay_them DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ThongKeChiTietTheoThang` ()   BEGIN
	SELECT 
        s.ma_sach,
        s.ten_sach,
        tg.ten_tac_gia,
        tl.ten_the_loai,
        s.so_luong,
        DATE_FORMAT(ngay_them, '%Y-%m') AS period
    FROM sach s
    INNER JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
    INNER JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
    ORDER BY period DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ThongKeDocGiaMuonNam` ()   BEGIN
    SELECT COUNT(DISTINCT ma_doc_gia) AS SoDocGiaMuon FROM phieu_muon
    WHERE YEAR(ngay_muon) = YEAR(CURDATE());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ThongKeDocGiaMuonNhieuNhat` (IN `startDate` DATE, IN `endDate` DATE)   BEGIN
    SELECT dg.ma_doc_gia, dg.ten_doc_gia, COUNT(pm.ma_phieu_muon) AS so_luot_muon
    FROM phieu_muon pm
    JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
    WHERE pm.ngay_muon BETWEEN startDate AND endDate
    GROUP BY dg.ma_doc_gia
    ORDER BY so_luot_muon DESC
    LIMIT 10; -- Top 10 độc giả mượn nhiều nhất
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ThongKeSachMuonNhieuNhat` (IN `startDate` DATE, IN `endDate` DATE)   BEGIN
    SELECT s.ma_sach, s.ten_sach, COUNT(ctpm.ma_ctpm) AS so_luot_muon
    FROM chi_tiet_phieu_muon ctpm
    JOIN sach s ON ctpm.ma_sach = s.ma_sach
JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
    WHERE pm.ngay_muon BETWEEN startDate AND endDate
    GROUP BY s.ma_sach
    ORDER BY so_luot_muon DESC
    LIMIT 10; -- Top 10 sách được mượn nhiều nhất
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `XuatBaoCaoMuonTra` (IN `thang` INT, IN `nam` INT)   BEGIN
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

--
-- Các hàm
--
CREATE DEFINER=`root`@`localhost` FUNCTION `get_top_genre_by_reader` (`reader_id` INT) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
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

CREATE DEFINER=`root`@`localhost` FUNCTION `KiemTraDocGiaDangMuon` (`ma_doc_gia` INT) RETURNS TINYINT(1) DETERMINISTIC BEGIN
    DECLARE so_luong_muon INT;
    
    SELECT COUNT(*) INTO so_luong_muon
    FROM phieu_muon
    WHERE phieu_muon.ma_doc_gia = ma_doc_gia AND trang_thai = 'Đang mượn';
    
    RETURN so_luong_muon > 0;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `KiemTraSoLuongSach` (`maSach` INT) RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE soLuongCon INT;
    SELECT so_luong INTO soLuongCon FROM sach WHERE ma_sach = maSach;
    RETURN soLuongCon;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `tinh_tong_tien_phat` (`maDocGia` INT) RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE tongTien INT;
    SELECT COALESCE(SUM(pt.tien_phat), 0) INTO tongTien 
    FROM phieu_tra pt
    JOIN chi_tiet_phieu_muon ctpm ON ctpm.ma_ctpm = pt.ma_ctpm
    JOIN phieu_muon pm ON pm.ma_phieu_muon = ctpm.ma_phieu_muon
    WHERE pm.ma_doc_gia = maDocGia;
    RETURN tongTien;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `total_borrows` (`period_type` VARCHAR(10)) RETURNS INT(11) DETERMINISTIC BEGIN
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
    ELSE
        -- Trả về 0 nếu giá trị `period_type` không hợp lệ
        SET total = 0;
    END IF;
    RETURN total;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_phieu_muon`
--

CREATE TABLE `chi_tiet_phieu_muon` (
  `ma_ctpm` int(11) NOT NULL,
  `ma_phieu_muon` int(11) DEFAULT NULL,
  `ma_sach` int(11) DEFAULT NULL,
  `so_luong` int(11) NOT NULL CHECK (`so_luong` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_phieu_muon`
--

INSERT INTO `chi_tiet_phieu_muon` (`ma_ctpm`, `ma_phieu_muon`, `ma_sach`, `so_luong`) VALUES
(2, 2, 1, 1),
(5, 4, 1, 1),
(6, 5, 1, 1),
(7, 6, 1, 1),
(8, 7, 1, 1),
(9, 8, 2, 1),
(10, 9, 1, 1),
(11, 9, 4, 2),
(12, 9, 3, 1),
(13, 10, 3, 1),
(14, 11, 16, 2),
(15, 12, 16, 2),
(16, 13, 20, 1),
(17, 14, 11, 1),
(18, 15, 10, 2),
(19, 16, 7, 1),
(20, 17, 19, 2),
(21, 18, 13, 1),
(22, 18, 9, 1),
(23, 19, 12, 1),
(24, 19, 17, 3),
(25, 20, 2, 1),
(26, 20, 3, 2),
(27, 20, 5, 1),
(28, 21, 14, 1),
(29, 22, 8, 3),
(30, 22, 13, 1);

--
-- Bẫy `chi_tiet_phieu_muon`
--
DELIMITER $$
CREATE TRIGGER `CapNhatSoLuongKhiMuon` AFTER INSERT ON `chi_tiet_phieu_muon` FOR EACH ROW BEGIN
    UPDATE sach 
    SET so_luong = so_luong - 1 
    WHERE ma_sach = NEW.ma_sach;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `KiemTraSoLuongTruocKhiThem` BEFORE INSERT ON `chi_tiet_phieu_muon` FOR EACH ROW BEGIN
    DECLARE soLuongCon INT;
    SET soLuongCon = (SELECT so_luong FROM sach WHERE ma_sach = NEW.ma_sach);
    
    IF soLuongCon < 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Số lượng sách không đủ!';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `doc_gia`
--

CREATE TABLE `doc_gia` (
  `ma_doc_gia` int(11) NOT NULL,
  `ten_doc_gia` varchar(255) NOT NULL,
  `ngay_sinh` date NOT NULL,
  `so_dien_thoai` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `doc_gia`
--

INSERT INTO `doc_gia` (`ma_doc_gia`, `ten_doc_gia`, `ngay_sinh`, `so_dien_thoai`, `created_at`, `email`) VALUES
(1, 'Mai Nhật Khang', '2004-12-05', '0362385725', '2025-03-08 12:54:48', ''),
(4, 'Trương Thy Minh', '2004-02-01', '0362385724', '2025-03-10 17:13:11', 'mainhatkhanghug12@gmail.com'),
(6, 'Dương Quốc Kiệt', '2004-11-05', '0365808645', '2025-03-14 08:01:00', ''),
(7, 'Trần Nguyễn Nhựt Trường', '2004-11-06', '0368699542', '2025-03-14 08:01:33', ''),
(8, 'Trịnh Anh Hào', '2004-08-02', '0369855899', '2025-03-14 08:01:54', ''),
(9, 'Nguyễn Thị Trà My', '2004-02-28', '0935968965', '2025-03-14 08:02:20', ''),
(10, 'Tăng Trần Gia Thịnh', '2004-11-05', '0365359858', '2025-03-14 08:02:52', ''),
(11, 'Võ Thị Hồng Ánh', '2004-03-08', '0932892693', '2025-03-14 08:03:29', '');

--
-- Bẫy `doc_gia`
--
DELIMITER $$
CREATE TRIGGER `CamXoaDocGiaNeuConMuonSach` BEFORE DELETE ON `doc_gia` FOR EACH ROW BEGIN
    IF (SELECT KiemTraDocGiaDangMuon(OLD.ma_doc_gia)) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Không thể xóa độc giả khi họ vẫn còn sách chưa trả';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieu_muon`
--

CREATE TABLE `phieu_muon` (
  `ma_phieu_muon` int(11) NOT NULL,
  `ma_doc_gia` int(11) DEFAULT NULL,
  `ngay_muon` date NOT NULL,
  `ngay_tra` date NOT NULL,
  `trang_thai` enum('Đang mượn','Đã trả') NOT NULL DEFAULT 'Đang mượn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phieu_muon`
--

INSERT INTO `phieu_muon` (`ma_phieu_muon`, `ma_doc_gia`, `ngay_muon`, `ngay_tra`, `trang_thai`) VALUES
(2, 1, '2025-03-08', '2025-03-15', 'Đã trả'),
(4, 1, '2025-02-27', '2025-02-27', 'Đã trả'),
(5, 1, '2025-02-27', '2025-02-27', 'Đã trả'),
(6, 1, '2025-02-27', '2025-02-27', 'Đã trả'),
(7, 1, '2025-03-09', '2025-03-09', 'Đã trả'),
(8, 1, '2025-03-09', '2025-03-09', 'Đã trả'),
(9, 1, '2025-03-10', '2025-03-11', 'Đã trả'),
(10, 4, '2025-03-11', '2025-03-13', 'Đã trả'),
(11, 1, '2025-03-11', '2025-03-11', 'Đã trả'),
(12, 1, '2025-03-11', '2025-03-11', 'Đã trả'),
(13, 4, '2025-03-12', '2025-03-12', 'Đã trả'),
(14, 4, '2025-03-12', '2025-03-13', 'Đã trả'),
(15, 1, '2025-03-13', '2025-03-13', 'Đã trả'),
(16, 1, '2025-03-12', '2025-03-12', 'Đã trả'),
(17, 1, '2025-03-12', '2025-03-13', 'Đã trả'),
(18, 4, '2025-03-12', '2025-03-13', 'Đang mượn'),
(19, 1, '2025-03-12', '2025-03-13', 'Đang mượn'),
(20, 1, '2025-03-12', '2025-03-12', 'Đang mượn'),
(21, 4, '2025-03-12', '2025-03-15', 'Đã trả'),
(22, 4, '2025-03-14', '2025-03-20', 'Đã trả');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieu_tra`
--

CREATE TABLE `phieu_tra` (
  `ma_phieu_tra` int(11) NOT NULL,
  `ma_ctpm` int(11) DEFAULT NULL,
  `ngay_tra_sach` date NOT NULL,
  `tien_phat` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phieu_tra`
--

INSERT INTO `phieu_tra` (`ma_phieu_tra`, `ma_ctpm`, `ngay_tra_sach`, `tien_phat`) VALUES
(1, 2, '2025-03-08', 0.00),
(2, 3, '2025-03-09', 0.00),
(6, 6, '2025-03-09', 752000.00),
(7, 7, '2025-03-09', 752000.00),
(8, 5, '2025-03-09', 752000.00),
(10, 8, '2025-03-09', 0.00),
(13, 9, '2025-03-09', 0.00),
(14, 10, '2025-03-10', 0.00),
(15, 11, '2025-03-10', 0.00),
(16, 12, '2025-03-10', 0.00),
(17, 13, '2025-03-10', 0.00),
(18, 15, '2025-03-18', 70000.00),
(19, 14, '2025-03-11', 0.00),
(20, 16, '2025-03-18', 60000.00),
(22, 17, '2025-03-18', 50000.00),
(23, 18, '2025-03-18', 50000.00),
(24, 18, '2025-03-18', 50000.00),
(25, 29, '2025-03-15', 0.00),
(26, 30, '2025-03-15', 0.00),
(27, 28, '2025-03-15', 0.00),
(28, 19, '2025-03-15', 30000.00),
(29, 20, '2025-03-15', 20000.00);

--
-- Bẫy `phieu_tra`
--
DELIMITER $$
CREATE TRIGGER `CapNhatSoLuongKhiTra` AFTER INSERT ON `phieu_tra` FOR EACH ROW BEGIN
    DECLARE soLuongMuon INT;
    SELECT COUNT(*) INTO soLuongMuon FROM chi_tiet_phieu_muon WHERE ma_ctpm = NEW.ma_ctpm;
    
    UPDATE sach 
    SET so_luong = so_luong + soLuongMuon 
    WHERE ma_sach = (SELECT ma_sach FROM chi_tiet_phieu_muon WHERE ma_ctpm = NEW.ma_ctpm);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `TinhTienPhat` BEFORE INSERT ON `phieu_tra` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sach`
--

CREATE TABLE `sach` (
  `ma_sach` int(11) NOT NULL,
  `ma_tac_gia` int(11) DEFAULT NULL,
  `ma_the_loai` int(11) DEFAULT NULL,
  `ten_sach` varchar(255) NOT NULL,
  `nam_xuat_ban` year(4) NOT NULL,
  `nha_xuat_ban` varchar(255) DEFAULT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 0,
  `ngay_them` date NOT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sach`
--

INSERT INTO `sach` (`ma_sach`, `ma_tac_gia`, `ma_the_loai`, `ten_sach`, `nam_xuat_ban`, `nha_xuat_ban`, `so_luong`, `ngay_them`) VALUES
(1, 1, 1, 'Cho Tôi Xin Một Vé Đi Tuổi Thơ', '2008', 'NXB Trẻ', 18, '2025-03-12'),
(2, 2, 2, 'Harry Potter và Hòn Đá Phù Thủy', '1997', 'Bloomsbury', 15, '2025-03-12'),
(3, 3, 3, 'Sapiens: Lược Sử Loài Người', '2011', 'HarperCollins', 9, '2025-03-12'),
(4, 4, 4, 'Đắc Nhân Tâm', '1936', 'Simon & Schuster', 20, '2025-03-12'),
(5, 5, 5, 'Doraemon Tập 1', '1970', 'Shogakukan', 23, '2025-03-12'),
(6, 6, 6, 'Thám Tử Lừng Danh Conan Tập 1', '1994', 'Shogakukan', 30, '2025-03-12'),
(7, 7, 7, 'Cha Giàu Cha Nghèo', '1997', 'Plata Publishing', 18, '2025-03-12'),
(8, 8, 8, 'Giáo Trình Cấu Trúc Dữ Liệu và Giải Thuật', '2010', 'NXB Giáo Dục', 10, '2025-03-12'),
(9, 9, 9, 'Project Hail Mary', '2021', 'Ballantine Books', 7, '2025-03-12'),
(10, 10, 10, 'Tomorrow, and Tomorrow, and Tomorrow', '2022', 'Knopf', 10, '2025-03-12'),
(11, 11, 1, 'Nhà Giả Kim', '1988', 'HarperTorch', 20, '2025-03-12'),
(12, 12, 10, 'Norwegian Wood', '1987', 'Kodansha', 13, '2025-03-12'),
(13, 13, 6, 'Mật Mã Da Vinci', '2003', 'Doubleday', 16, '2025-03-12'),
(14, 14, 9, '1984', '1949', 'Secker & Warburg', 25, '2025-03-12'),
(15, 15, 1, 'Phía Tây Không Có Gì Lạ', '1929', 'Propyläen Verlag', 12, '2025-03-12'),
(16, 16, 3, 'Lược Sử Thời Gian', '1988', 'Bantam Books', 20, '2025-03-12'),
(17, 17, 10, 'Hoàng Tử Bé', '1943', 'Reynal & Hitchcock', 26, '2025-03-12'),
(18, 18, 4, 'Think and Grow Rich', '1937', 'The Ralston Society', 17, '2025-03-12'),
(19, 19, 4, 'Outliers: The Story of Success', '2008', 'Little, Brown and Company', 14, '2025-03-12'),
(20, 20, 4, 'Atomic Habits', '2018', 'Avery', 21, '2025-03-12'),
(49, 21, 4, 'Trần nguyẽn nhật trường', '2004', 'kim đồng', 20, '2025-03-14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tac_gia`
--

CREATE TABLE `tac_gia` (
  `ma_tac_gia` int(11) NOT NULL,
  `ten_tac_gia` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tac_gia`
--

INSERT INTO `tac_gia` (`ma_tac_gia`, `ten_tac_gia`) VALUES
(17, 'Antoine de Saint-Exupéry'),
(4, 'Dale Carnegie'),
(13, 'Dan Brown'),
(21, 'Dương QUốc kIệt'),
(15, 'Erich Maria Remarque'),
(5, 'Fujiko F. Fujio'),
(10, 'Gabrielle Zevin'),
(14, 'George Orwell'),
(6, 'Gosho Aoyama'),
(12, 'Haruki Murakami'),
(2, 'J.K. Rowling'),
(20, 'James Clear'),
(8, 'Mai Nhật Khang'),
(19, 'Malcolm Gladwell'),
(18, 'Napoleon Hill'),
(1, 'Nguyễn Nhật Ánh'),
(11, 'Paulo Coelho'),
(9, 'Robert C. Martin'),
(7, 'Robert T. Kiyosaki'),
(16, 'Stephen Hawking'),
(3, 'Yuval Noah Harari');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `the_loai`
--

CREATE TABLE `the_loai` (
  `ma_the_loai` int(11) NOT NULL,
  `ten_the_loai` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `the_loai`
--

INSERT INTO `the_loai` (`ma_the_loai`, `ten_the_loai`) VALUES
(1, 'Tiểu thuyết'),
(2, 'Khoa học viễn tưởng'),
(3, 'Tiểu thuyết'),
(4, 'Giả tưởng'),
(5, 'Lịch sử'),
(6, 'Kỹ năng sống'),
(7, 'Truyện tranh'),
(8, 'Trinh thám'),
(9, 'Kinh doanh'),
(10, 'Công nghệ'),
(11, 'Khoa học viễn tưởng'),
(12, 'Tiểu thuyết đương đại'),
(13, 'Tiểu thuyết'),
(14, 'Giả tưởng'),
(15, 'Lịch sử'),
(16, 'Kỹ năng sống'),
(17, 'Truyện tranh'),
(18, 'Trinh thám'),
(19, 'Kinh doanh'),
(20, 'Công nghệ'),
(21, 'Khoa học viễn tưởng'),
(22, 'Tiểu thuyết đương đại');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chi_tiet_phieu_muon`
--
ALTER TABLE `chi_tiet_phieu_muon`
  ADD PRIMARY KEY (`ma_ctpm`),
  ADD KEY `ma_phieu_muon` (`ma_phieu_muon`),
  ADD KEY `ma_sach` (`ma_sach`);

--
-- Chỉ mục cho bảng `doc_gia`
--
ALTER TABLE `doc_gia`
  ADD PRIMARY KEY (`ma_doc_gia`),
  ADD UNIQUE KEY `so_dien_thoai` (`so_dien_thoai`);

--
-- Chỉ mục cho bảng `phieu_muon`
--
ALTER TABLE `phieu_muon`
  ADD PRIMARY KEY (`ma_phieu_muon`),
  ADD KEY `ma_doc_gia` (`ma_doc_gia`);

--
-- Chỉ mục cho bảng `phieu_tra`
--
ALTER TABLE `phieu_tra`
  ADD PRIMARY KEY (`ma_phieu_tra`),
  ADD KEY `ma_ctpm` (`ma_ctpm`);

--
-- Chỉ mục cho bảng `sach`
--
ALTER TABLE `sach`
  ADD PRIMARY KEY (`ma_sach`),
  ADD KEY `ma_tac_gia` (`ma_tac_gia`),
  ADD KEY `ma_the_loai` (`ma_the_loai`);

--
-- Chỉ mục cho bảng `tac_gia`
--
ALTER TABLE `tac_gia`
  ADD PRIMARY KEY (`ma_tac_gia`),
  ADD UNIQUE KEY `ten_tac_gia` (`ten_tac_gia`);

--
-- Chỉ mục cho bảng `the_loai`
--
ALTER TABLE `the_loai`
  ADD PRIMARY KEY (`ma_the_loai`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chi_tiet_phieu_muon`
--
ALTER TABLE `chi_tiet_phieu_muon`
  MODIFY `ma_ctpm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT cho bảng `doc_gia`
--
ALTER TABLE `doc_gia`
  MODIFY `ma_doc_gia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `phieu_muon`
--
ALTER TABLE `phieu_muon`
  MODIFY `ma_phieu_muon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `phieu_tra`
--
ALTER TABLE `phieu_tra`
  MODIFY `ma_phieu_tra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `sach`
--
ALTER TABLE `sach`
  MODIFY `ma_sach` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT cho bảng `tac_gia`
--
ALTER TABLE `tac_gia`
  MODIFY `ma_tac_gia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `the_loai`
--
ALTER TABLE `the_loai`
  MODIFY `ma_the_loai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chi_tiet_phieu_muon`
--
ALTER TABLE `chi_tiet_phieu_muon`
  ADD CONSTRAINT `chi_tiet_phieu_muon_ibfk_1` FOREIGN KEY (`ma_phieu_muon`) REFERENCES `phieu_muon` (`ma_phieu_muon`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_phieu_muon_ibfk_2` FOREIGN KEY (`ma_sach`) REFERENCES `sach` (`ma_sach`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `phieu_muon`
--
ALTER TABLE `phieu_muon`
  ADD CONSTRAINT `phieu_muon_ibfk_1` FOREIGN KEY (`ma_doc_gia`) REFERENCES `doc_gia` (`ma_doc_gia`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `phieu_tra`
--
SELECT ma_ctpm FROM chi_tiet_phieu_muon;
SELECT ma_ctpm FROM phieu_tra;

DELETE FROM phieu_tra WHERE ma_ctpm NOT IN (SELECT ma_ctpm FROM chi_tiet_phieu_muon);


ALTER TABLE `phieu_tra`
  ADD CONSTRAINT `phieu_tra_ibfk_1` FOREIGN KEY (`ma_ctpm`) REFERENCES `chi_tiet_phieu_muon` (`ma_ctpm`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `sach`
--
ALTER TABLE `sach`
  ADD CONSTRAINT `sach_ibfk_1` FOREIGN KEY (`ma_tac_gia`) REFERENCES `tac_gia` (`ma_tac_gia`) ON DELETE CASCADE,
  ADD CONSTRAINT `sach_ibfk_2` FOREIGN KEY (`ma_the_loai`) REFERENCES `the_loai` (`ma_the_loai`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
