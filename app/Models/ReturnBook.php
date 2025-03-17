<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Core\Model;
use App\Models\Penalty;

class ReturnBook extends Model
{
    protected $table = 'phieu_tra';

    // Lấy tất cả các phiếu trả
    public function getAllReturns()
    {
        $query = "SELECT 
    pt.ma_phieu_tra, 
    pm.ma_phieu_muon, 
    ctpm.ma_ctpm, 
    pm.ma_doc_gia, 
    pm.ngay_muon, 
    pm.ngay_tra AS ngay_tra_du_kien, 
    pt.ngay_tra_sach AS ngay_tra_thuc_te, 
    pt.tien_phat 
FROM phieu_muon pm
JOIN chi_tiet_phieu_muon ctpm ON pm.ma_phieu_muon = ctpm.ma_phieu_muon
JOIN phieu_tra pt ON pt.ma_ctpm = ctpm.ma_ctpm
JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
GROUP BY 
    pt.ma_phieu_tra, 
    pm.ma_phieu_muon, 
    ctpm.ma_ctpm, 
    pm.ma_doc_gia, 
    pm.ngay_muon, 
    pm.ngay_tra, 
    pt.ngay_tra_sach, 
    pt.tien_phat
ORDER BY pt.ma_phieu_tra DESC;
";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xác nhận trả sách
    public function returnBook($ma_phieu_muon, $ngay_tra_sach)
    {
        // Lấy danh sách chi tiết phiếu mượn
        $sql = "SELECT ma_ctpm, ma_sach, so_luong FROM chi_tiet_phieu_muon WHERE ma_phieu_muon = :ma_phieu_muon";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":ma_phieu_muon", $ma_phieu_muon, PDO::PARAM_INT);
        $stmt->execute();
        $chiTietPhieuMuon = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$chiTietPhieuMuon) {
            return false; // Không có chi tiết phiếu mượn nào
        }

        $this->db->beginTransaction();
        try {
            foreach ($chiTietPhieuMuon as $chiTiet) {
                $ma_ctpm = $chiTiet['ma_ctpm'];
                $ma_sach = $chiTiet['ma_sach'];
                $so_luong = $chiTiet['so_luong'];

                // Chèn vào bảng trả sách
                $query = "INSERT INTO {$this->table} (ma_ctpm, ngay_tra_sach) VALUES (:ma_ctpm, :ngay_tra_sach)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':ma_ctpm', $ma_ctpm, PDO::PARAM_INT);
                $stmt->bindParam(':ngay_tra_sach', $ngay_tra_sach);
                $stmt->execute();

                // Cập nhật số lượng sách
                $query = "UPDATE sach SET so_luong = so_luong + :so_luong WHERE ma_sach = :ma_sach";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':so_luong', $so_luong, PDO::PARAM_INT);
                $stmt->bindParam(':ma_sach', $ma_sach, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Kiểm tra xem có bị phạt không
            $query = "UPDATE phieu_muon SET trang_thai = 'Đã trả' WHERE ma_phieu_muon = :ma_phieu_muon";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':ma_phieu_muon', $ma_phieu_muon, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getReturnDetail($ma_phieu_muon)
    {
        // Lấy thông tin phiếu trả
        $query = "SELECT pt.*, pm.ma_phieu_muon, pm.ngay_muon, pm.ngay_tra, dg.ten_doc_gia, dg.so_dien_thoai 
                  FROM phieu_tra pt
                  JOIN chi_tiet_phieu_muon ctpm ON pt.ma_ctpm = ctpm.ma_ctpm
                  JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
                  JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
                  WHERE pm.ma_phieu_muon = :ma_phieu_muon";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_phieu_muon', $ma_phieu_muon);
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
