<?php
namespace App\Models;
use PDO;
use PDOException;
use App\Core\Model;
class ReturnBook extends Model {
    protected $table = 'phieu_tra';

    // Lấy tất cả các phiếu trả
    public function getAllReturns() {
        $query = "SELECT pt.ma_phieu_tra, pm.ma_phieu_muon, ctpm.ma_ctpm, pm.ngay_muon, pm.ngay_tra as ngay_tra_du_kien, pt.ngay_tra_sach as ngay_tra_thuc_te
                    ,pt.tien_phat
                    FROM phieu_muon pm
                    JOIN chi_tiet_phieu_muon ctpm ON pm.ma_phieu_muon = ctpm.ma_phieu_muon
                    join phieu_tra as pt on pt.ma_ctpm = ctpm.ma_ctpm
                    GROUP BY pm.ma_phieu_muon, pm.ma_doc_gia, pm.ngay_muon, pm.ngay_tra, pm.trang_thai
                    ORDER BY pt.ma_phieu_tra DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xác nhận trả sách
    public function returnBook($ma_ctpm, $ngay_tra_sach) {
        $this->db->beginTransaction();
        try {
            // Thêm phiếu trả
            $query = "INSERT INTO {$this->table} (ma_ctpm, ngay_tra_sach) VALUES (:ma_ctpm, :ngay_tra_sach)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':ma_ctpm', $ma_ctpm);
            $stmt->bindParam(':ngay_tra_sach', $ngay_tra_sach);
            $stmt->execute();

            // Cập nhật trạng thái phiếu mượn
            $query = "UPDATE phieu_muon SET trang_thai = 'Đã trả' 
                      WHERE ma_phieu_muon = (SELECT ma_phieu_muon FROM chi_tiet_phieu_muon WHERE ma_ctpm = :ma_ctpm)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':ma_ctpm', $ma_ctpm);
            $stmt->execute();

            // Tăng số lượng sách hiện có
            $query = "UPDATE sach SET so_luong = so_luong + (SELECT so_luong FROM chi_tiet_phieu_muon WHERE ma_ctpm = :ma_ctpm) 
                      WHERE ma_sach = (SELECT ma_sach FROM chi_tiet_phieu_muon WHERE ma_ctpm = :ma_ctpm)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':ma_ctpm', $ma_ctpm);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
    public function getReturnDetail($ma_phieu_tra) {
        // Lấy thông tin phiếu trả
        $query = "SELECT pt.*, pm.ma_phieu_muon, pm.ngay_muon, pm.ngay_tra, dg.ten_doc_gia, dg.so_dien_thoai 
                  FROM phieu_tra pt
                  JOIN chi_tiet_phieu_muon ctpm ON pt.ma_ctpm = ctpm.ma_ctpm
                  JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
                  JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
                  WHERE pt.ma_phieu_tra = :ma_phieu_tra";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_phieu_tra', $ma_phieu_tra);
        $stmt->execute();
        $returnInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$returnInfo) {
            return null; // Không tìm thấy phiếu trả
        }

        // Lấy danh sách sách trong phiếu mượn
        $query = "SELECT ctpm.*, s.ten_sach, tg.ten_tac_gia
                  FROM chi_tiet_phieu_muon ctpm
                  JOIN sach s ON ctpm.ma_sach = s.ma_sach
                  join tac_gia tg on tg.ma_tac_gia = s.ma_tac_gia
                  WHERE ctpm.ma_phieu_muon = :ma_phieu_muon";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_phieu_muon', $returnInfo['ma_phieu_muon']);
        $stmt->execute();
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Kết hợp thông tin
        $returnInfo['books'] = $books;
        return $returnInfo;
    }
}