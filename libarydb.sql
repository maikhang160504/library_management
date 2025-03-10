-- Tạo cơ sở dữ liệu
CREATE DATABASE IF NOT EXISTS LibraryDB;
USE LibraryDB;

-- Tạo bảng Tác Giả
CREATE TABLE tac_gia (
    ma_tac_gia INT PRIMARY KEY AUTO_INCREMENT,
    ten_tac_gia VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Tạo bảng Thể Loại
CREATE TABLE the_loai (
    ma_the_loai INT PRIMARY KEY AUTO_INCREMENT,
    ten_the_loai VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Tạo bảng Sách
CREATE TABLE sach (
    ma_sach INT PRIMARY KEY AUTO_INCREMENT,
    ma_tac_gia INT,
    ma_the_loai INT,
    ten_sach VARCHAR(255) NOT NULL,
    nam_xuat_ban YEAR NOT NULL,
    nha_xuat_ban VARCHAR(255),
    so_luong INT NOT NULL DEFAULT 0,
    FOREIGN KEY (ma_tac_gia) REFERENCES tac_gia(ma_tac_gia) ON DELETE CASCADE,
    FOREIGN KEY (ma_the_loai) REFERENCES the_loai(ma_the_loai) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tạo bảng Độc Giả
CREATE TABLE doc_gia (
    ma_doc_gia INT PRIMARY KEY AUTO_INCREMENT,
    ten_doc_gia VARCHAR(255) NOT NULL,
    ngay_sinh DATE NOT NULL,
    so_dien_thoai VARCHAR(15) UNIQUE NOT NULL
) ENGINE=InnoDB;

-- Tạo bảng Phiếu Mượn
CREATE TABLE phieu_muon (
    ma_phieu_muon INT PRIMARY KEY AUTO_INCREMENT,
    ma_doc_gia INT,
    ngay_muon DATE NOT NULL,
    ngay_tra DATE NOT NULL,
    trang_thai ENUM('Đang mượn', 'Đã trả') NOT NULL DEFAULT 'Đang mượn',
    FOREIGN KEY (ma_doc_gia) REFERENCES doc_gia(ma_doc_gia) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tạo bảng Chi Tiết Phiếu Mượn
CREATE TABLE chi_tiet_phieu_muon (
    ma_ctpm INT PRIMARY KEY AUTO_INCREMENT,
    ma_phieu_muon INT,
    ma_sach INT,
    so_luong INT NOT NULL CHECK (so_luong > 0),
    FOREIGN KEY (ma_phieu_muon) REFERENCES phieu_muon(ma_phieu_muon) ON DELETE CASCADE,
    FOREIGN KEY (ma_sach) REFERENCES sach(ma_sach) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tạo bảng Phiếu Trả
CREATE TABLE phieu_tra (
    ma_phieu_tra INT PRIMARY KEY AUTO_INCREMENT,
    ma_ctpm INT,
    ngay_tra_sach DATE NOT NULL,
    tien_phat DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (ma_ctpm) REFERENCES chi_tiet_phieu_muon(ma_ctpm) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ========================== TÍNH NĂNG MYSQL ==========================

-- 📌 FUNCTION: Kiểm tra số lượng sách còn lại trong thư viện
DELIMITER //
CREATE FUNCTION SoLuongSachConLai(maSach INT) RETURNS INT DETERMINISTIC
BEGIN
    DECLARE so_luong_con INT;
    SELECT so_luong INTO so_luong_con FROM sach WHERE ma_sach = maSach;
    RETURN so_luong_con;
END //
DELIMITER ;

-- 📌 TRIGGER: Tự động tính tiền phạt khi trả sách muộn (2,000 VND/ngày)
DELIMITER //
CREATE TRIGGER TinhTienPhat
BEFORE INSERT ON phieu_tra
FOR EACH ROW
BEGIN
    DECLARE ngay_muon DATE;
    DECLARE so_ngay_muon INT;
    
    -- Lấy ngày mượn từ bảng phiếu mượn
    SELECT ngay_muon INTO ngay_muon FROM phieu_muon
    JOIN chi_tiet_phieu_muon ON phieu_muon.ma_phieu_muon = chi_tiet_phieu_muon.ma_phieu_muon
    WHERE chi_tiet_phieu_muon.ma_ctpm = NEW.ma_ctpm;

    -- Tính số ngày trễ hạn
    SET so_ngay_muon = DATEDIFF(NEW.ngay_tra_sach, ngay_muon);
    
    -- Nếu trả muộn hơn 7 ngày, tính tiền phạt
    IF so_ngay_muon > 7 THEN
        SET NEW.tien_phat = (so_ngay_muon - 7) * 2000;
    ELSE
        SET NEW.tien_phat = 0;
    END IF;
END //
DELIMITER ;

-- 📌 STORED PROCEDURE: Lấy danh sách phiếu mượn chưa trả
DELIMITER //
CREATE PROCEDURE LayPhieuMuonChuaTra()
BEGIN
    SELECT * FROM phieu_muon WHERE trang_thai = 'Đang mượn';
END //
DELIMITER ;

-- 📌 THỐNG KÊ: Số lượng sách mượn trong tháng hiện tại
DELIMITER //
CREATE PROCEDURE ThongKeSachMuonThang()
BEGIN
    SELECT COUNT(*) AS SoSachMuon FROM chi_tiet_phieu_muon
    JOIN phieu_muon ON chi_tiet_phieu_muon.ma_phieu_muon = phieu_muon.ma_phieu_muon
    WHERE MONTH(phieu_muon.ngay_muon) = MONTH(CURDATE()) AND YEAR(phieu_muon.ngay_muon) = YEAR(CURDATE());
END //
DELIMITER ;

-- 📌 THỐNG KÊ: Số độc giả mượn sách trong năm hiện tại
DELIMITER //
CREATE PROCEDURE ThongKeDocGiaMuonNam()
BEGIN
    SELECT COUNT(DISTINCT ma_doc_gia) AS SoDocGiaMuon FROM phieu_muon
    WHERE YEAR(ngay_muon) = YEAR(CURDATE());
END //
DELIMITER ;

-- ========================== DỮ LIỆU MẪU ==========================
INSERT INTO tac_gia (ten_tac_gia) VALUES ('Nguyễn Nhật Ánh'), ('J.K. Rowling');
INSERT INTO the_loai (ten_the_loai) VALUES ('Tiểu thuyết'), ('Khoa học viễn tưởng');
INSERT INTO sach (ma_tac_gia, ma_the_loai, ten_sach, nam_xuat_ban, nha_xuat_ban, so_luong) 
VALUES 
    (1, 1, 'Cho Tôi Xin Một Vé Đi Tuổi Thơ', 2008, 'NXB Trẻ', 10),
    (2, 2, 'Harry Potter và Hòn Đá Phù Thủy', 1997, 'Bloomsbury', 15);

INSERT INTO doc_gia (ten_doc_gia, ngay_sinh, so_dien_thoai) 
VALUES ('Nguyễn Văn A', '2000-05-10', '0987654321');

INSERT INTO phieu_muon (ma_doc_gia, ngay_muon, ngay_tra, trang_thai) 
VALUES (1, '2025-03-01', '2025-03-10', 'Đang mượn');

INSERT INTO chi_tiet_phieu_muon (ma_phieu_muon, ma_sach, so_luong) 
VALUES (1, 1, 2);


INSERT INTO tac_gia (ten_tac_gia) VALUES
('Nguyễn Nhật Ánh'),
('J.K. Rowling'),
('Yuval Noah Harari'),
('Dale Carnegie'),
('Fujiko F. Fujio'),
('Gosho Aoyama'),
('Robert T. Kiyosaki'),
('Mai Nhật Khang'),
('Robert C. Martin'),
('Gabrielle Zevin');

INSERT INTO the_loai (ten_the_loai) VALUES
('Tiểu thuyết'),
('Giả tưởng'),
('Lịch sử'),
('Kỹ năng sống'),
('Truyện tranh'),
('Trinh thám'),
('Kinh doanh'),
('Công nghệ'),
('Khoa học viễn tưởng'),
('Tiểu thuyết đương đại');

INSERT INTO sach (ma_tac_gia, ma_the_loai, ten_sach, nam_xuat_ban, nha_xuat_ban, so_luong) VALUES
(1, 1, 'Cho Tôi Xin Một Vé Đi Tuổi Thơ', 2008, 'NXB Trẻ', 10),
(2, 2, 'Harry Potter và Hòn Đá Phù Thủy', 1997, 'Bloomsbury', 15),
(3, 3, 'Sapiens: Lược Sử Loài Người', 2011, 'HarperCollins', 12),
(4, 4, 'Đắc Nhân Tâm', 1936, 'Simon & Schuster', 20),
(5, 5, 'Doraemon Tập 1', 1970, 'Shogakukan', 25),
(6, 6, 'Thám Tử Lừng Danh Conan Tập 1', 1994, 'Shogakukan', 30),
(7, 7, 'Cha Giàu Cha Nghèo', 1997, 'Plata Publishing', 18),
(8, 8, 'Giáo Trình Cấu Trúc Dữ Liệu và Giải Thuật', 2010, 'NXB Giáo Dục', 10),
(9, 9, 'Project Hail Mary', 2021, 'Ballantine Books', 9),
(10, 10, 'Tomorrow, and Tomorrow, and Tomorrow', 2022, 'Knopf', 7);

INSERT INTO sach (ma_tac_gia, ma_the_loai, ten_sach, nam_xuat_ban, nha_xuat_ban, so_luong) VALUES
(11, 1, 'Nhà Giả Kim', 1988, 'HarperTorch', 20),
(12, 10, 'Norwegian Wood', 1987, 'Kodansha', 15),
(13, 6, 'Mật Mã Da Vinci', 2003, 'Doubleday', 18),
(14, 9, '1984', 1949, 'Secker & Warburg', 25),
(15, 1, 'Phía Tây Không Có Gì Lạ', 1929, 'Propyläen Verlag', 12),
(16, 3, 'Lược Sử Thời Gian', 1988, 'Bantam Books', 22),
(17, 10, 'Hoàng Tử Bé', 1943, 'Reynal & Hitchcock', 30),
(18, 4, 'Think and Grow Rich', 1937, 'The Ralston Society', 17),
(19, 4, 'Outliers: The Story of Success', 2008, 'Little, Brown and Company', 14),
(20, 4, 'Atomic Habits', 2018, 'Avery', 19);

INSERT INTO tac_gia (ten_tac_gia) VALUES
('Paulo Coelho'),
('Haruki Murakami'),
('Dan Brown'),
('George Orwell'),
('Erich Maria Remarque'),
('Stephen Hawking'),
('Antoine de Saint-Exupéry'),
('Napoleon Hill'),
('Malcolm Gladwell'),
('James Clear');

CREATE TABLE phi_phat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reader_id INT,
    amount DECIMAL(10, 2),
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO doc_gia (ten_doc_gia, ngay_sinh, so_dien_thoai)
VALUES
('Nguyen Van A', '2000-01-01', '0912345678'),
('Tran Thi B', '1999-05-10', '0987654321'),
('Le Van C', '2002-09-15', '0909090909'),
('Pham Minh D', '1998-07-22', '0911002200'),
('Hoang Thi E', '2001-03-14', '0933445566'),
('Nguyen Van F', '1997-12-11', '0977334455'),
('Le Thi G', '1996-06-30', '0966778899'),
('Tran Van H', '2000-10-05', '0944223355'),
('Phan Thi I', '2003-04-25', '0955667788'),
('Dang Van J', '1995-11-18', '0922334455'),
('Vu Thi K', '1999-09-09', '0933555777'),
('Nguyen Van L', '1998-05-20', '0911223344'),
('Le Thi M', '2001-01-30', '0944885566'),
('Tran Van N', '2002-08-08', '0977555666'),
('Pham Thi O', '1997-04-12', '0966445577'),
('Hoang Van P', '2000-02-28', '0955778899'),
('Nguyen Thi Q', '2003-03-03', '0911667788'),
('Le Van R', '1996-07-07', '0922556677'),
('Tran Thi S', '1995-12-25', '0933221144'),
('Pham Van T', '2001-06-15', '0944778899');
