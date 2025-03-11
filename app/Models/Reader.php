<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Core\Model;

class Reader extends Model
{
    protected $table = 'doc_gia';

    // Lấy tất cả độc giả
    public function getAllReaders()
    {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy độc giả theo mã
    public function getReaderById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE ma_doc_gia = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Chỉ lấy 1 bản ghi
    }

    public function deleteReader($id)
    {
        try {
            $query = "DELETE FROM doc_gia WHERE ma_doc_gia = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return $e->getMessage(); // Bắt lỗi trigger và trả về thông báo
        }
    }


    public function isReaderBorrowing($readerId)
    {
        $query = "SELECT KiemTraDocGiaDangMuon(:readerId) AS isBorrowing";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':readerId', $readerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['isBorrowing'];
    }


    public function updateReader($id, $data)
{
    try {
        $sql = "CALL CapNhatDocGia(:ma_doc_gia, :ten_doc_gia, :ngay_sinh, :so_dien_thoai)";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'ma_doc_gia' => $id,
            'ten_doc_gia' => $data['ten_doc_gia'],
            'ngay_sinh' => $data['ngay_sinh'],
            'so_dien_thoai' => $data['so_dien_thoai']
        ]);

        return true;
    } catch (\PDOException $e) {
        return $e->getMessage(); // Trả về lỗi từ MySQL
    }
}


    public function addReader($data)
    {
        try {
            $sql = "CALL ThemDocGia(:ten_doc_gia, :ngay_sinh, :so_dien_thoai)";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                'ten_doc_gia' => $data['ten_doc_gia'],
                'ngay_sinh' => $data['ngay_sinh'],
                'so_dien_thoai' => $data['so_dien_thoai']
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }


    public function detailReader($id)
    {
        // Lấy thông tin chi tiết độc giả
        $query = "SELECT * FROM {$this->table} WHERE ma_doc_gia = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $reader = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reader) {
            return null; // Trả về null nếu không tìm thấy độc giả
        }

        // Lấy lịch sử mượn sách của độc giả
        $query = "
        SELECT 
            pm.ma_phieu_muon, 
            s.ten_sach, 
            pm.ngay_muon, 
            pm.ngay_tra AS ngay_tra_du_kien, 
            pt.ngay_tra_sach AS ngay_tra_thuc_te, 
            pt.tien_phat,
            pm.trang_thai AS trang_thai_pm,
            CASE 
                WHEN pm.trang_thai = 'Đã trả' THEN 'Đã thanh toán'
                ELSE 'Chưa thanh toán'
            END AS trang_thai_thanh_toan
        FROM chi_tiet_phieu_muon ctpm
        JOIN sach s ON ctpm.ma_sach = s.ma_sach
        JOIN phieu_muon pm ON pm.ma_phieu_muon = ctpm.ma_phieu_muon
        LEFT JOIN phieu_tra pt ON pt.ma_ctpm = ctpm.ma_ctpm
        WHERE pm.ma_doc_gia = :id
    ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $borrowHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'reader' => $reader,
            'borrowHistory' => $borrowHistory
        ];
    }

    public function searchReaders($keyword)
    {
        $sql = "SELECT * FROM {$this->table} 
            WHERE ma_doc_gia LIKE :keyword 
            OR ten_doc_gia LIKE :keyword 
            OR so_dien_thoai LIKE :keyword";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getReadersWithPagination($start, $perPage, $keyword = '')
    {
        if ($keyword) {
            $query = "SELECT * FROM doc_gia WHERE ten_doc_gia LIKE :keyword OR so_dien_thoai LIKE :keyword LIMIT :start, :perPage";
        } else {
            $query = "SELECT * FROM doc_gia LIMIT :start, :perPage";
        }

        $stmt = $this->db->prepare($query);
        if ($keyword) {
            $stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
        }
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalReaders($keyword = '')
    {
        if ($keyword) {
            $query = "SELECT COUNT(*) FROM doc_gia WHERE ten_doc_gia LIKE :keyword OR so_dien_thoai LIKE :keyword";
        } else {
            $query = "SELECT COUNT(*) FROM doc_gia";
        }

        $stmt = $this->db->prepare($query);
        if ($keyword) {
            $stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function checkPhoneExists($phone)
{
    $sql = "SELECT COUNT(*) FROM doc_gia WHERE so_dien_thoai = :so_dien_thoai";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['so_dien_thoai' => $phone]);
    return $stmt->fetchColumn() > 0;
}

}
