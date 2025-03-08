DELIMITER //
CREATE FUNCTION KiemTraSoLuongSach(maSach INT) RETURNS INT DETERMINISTIC
BEGIN
    DECLARE soLuongCon INT;
    SELECT so_luong INTO soLuongCon FROM sach WHERE ma_sach = maSach;
    RETURN soLuongCon;
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER KiemTraSoLuongTruocKhiThem
BEFORE INSERT ON chi_tiet_phieu_muon
FOR EACH ROW
BEGIN
    DECLARE soLuongCon INT;
    SET soLuongCon = KiemTraSoLuongSach(NEW.ma_sach);
    IF soLuongCon < NEW.so_luong THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Số lượng sách không đủ!';
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER CapNhatSoLuongKhiMuon
AFTER INSERT ON chi_tiet_phieu_muon
FOR EACH ROW
BEGIN
    UPDATE sach SET so_luong = so_luong - NEW.so_luong WHERE ma_sach = NEW.ma_sach;
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER CapNhatSoLuongKhiTra
AFTER INSERT ON phieu_tra
FOR EACH ROW
BEGIN
    DECLARE soLuongMuon INT;
    SELECT so_luong INTO soLuongMuon FROM chi_tiet_phieu_muon WHERE ma_ctpm = NEW.ma_ctpm;
    UPDATE sach SET so_luong = so_luong + soLuongMuon WHERE ma_sach = (SELECT ma_sach FROM chi_tiet_phieu_muon WHERE ma_ctpm = NEW.ma_ctpm);
END //
DELIMITER ;

DELIMITER //
CREATE EVENT GuiThongBaoNhacNho
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    DECLARE maDocGia INT;
    DECLARE tenDocGia VARCHAR(255);
    DECLARE emailDocGia VARCHAR(255);
    DECLARE ngayTra DATE;
    
    -- Lấy danh sách độc giả cần nhắc nhở
    DECLARE cur CURSOR FOR
    SELECT pm.ma_doc_gia, dg.ten_doc_gia, dg.email, pm.ngay_tra
    FROM phieu_muon pm
    JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
    WHERE pm.trang_thai = 'Đang mượn' AND DATEDIFF(pm.ngay_tra, CURDATE()) = 3; -- Nhắc nhở 3 ngày trước hạn

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO maDocGia, tenDocGia, emailDocGia, ngayTra;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Gửi email nhắc nhở (giả định có hàm SendEmail)
        CALL SendEmail(emailDocGia, 'Nhắc nhở trả sách', CONCAT('Xin chào ', tenDocGia, ', vui lòng trả sách trước ngày ', ngayTra));
    END LOOP;
    CLOSE cur;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE ThongKeSachMuonNhieuNhat(IN startDate DATE, IN endDate DATE)
BEGIN
    SELECT s.ma_sach, s.ten_sach, COUNT(ctpm.ma_ctpm) AS so_luot_muon
    FROM chi_tiet_phieu_muon ctpm
    JOIN sach s ON ctpm.ma_sach = s.ma_sach
    JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
    WHERE pm.ngay_muon BETWEEN startDate AND endDate
    GROUP BY s.ma_sach
    ORDER BY so_luot_muon DESC
    LIMIT 10; -- Top 10 sách được mượn nhiều nhất
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE ThongKeDocGiaMuonNhieuNhat(IN startDate DATE, IN endDate DATE)
BEGIN
    SELECT dg.ma_doc_gia, dg.ten_doc_gia, COUNT(pm.ma_phieu_muon) AS so_luot_muon
    FROM phieu_muon pm
    JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
    WHERE pm.ngay_muon BETWEEN startDate AND endDate
    GROUP BY dg.ma_doc_gia
    ORDER BY so_luot_muon DESC
    LIMIT 10; -- Top 10 độc giả mượn nhiều nhất
END //
DELIMITER ;

drop PROCEDURE XuatBaoCaoMuonTra;

DELIMITER //
CREATE PROCEDURE XuatBaoCaoMuonTra(IN thang INT, IN nam INT)
BEGIN
    SELECT pm.ma_phieu_muon, pm.ma_doc_gia, pm.ngay_muon, pm.ngay_tra, pm.trang_thai, pt.tien_phat
    FROM phieu_muon pm
	JOIN chi_tiet_phieu_muon ctpm ON pm.ma_phieu_muon = ctpm.ma_phieu_muon
    join phieu_tra pt on pt.ma_ctpm = ctpm.ma_ctpm
    WHERE MONTH(pm.ngay_muon) = thang AND YEAR(pm.ngay_muon) = nam;
END //
DELIMITER ;