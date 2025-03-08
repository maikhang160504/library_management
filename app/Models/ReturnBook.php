<?php
namespace App\Models;
use PDO;
use PDOException;
use App\Core\Model;
class ReturnBook extends Model {
    protected $table = 'phieu_tra';

    // Lấy tất cả các phiếu trả
    public function getAllReturns() {
        $query = "SELECT * FROM {$this->table}";
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
}