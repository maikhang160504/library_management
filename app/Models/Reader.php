<?php

namespace App\Models;

use PDO;
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

    // Xoá độc giả
    public function deleteReader($id)
    {
        $query = "DELETE FROM {$this->table} WHERE ma_doc_gia = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function isReaderBorrowing($readerId)
    {
        $query = "
        SELECT COUNT(*) 
        FROM phieu_muon 
        WHERE ma_doc_gia = :readerId AND trang_thai = 'Đang mượn'";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':readerId', $readerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }


    public function updateReader($id, $data)
    {
        $ten_doc_gia = $data['ten_doc_gia'];
        $ngay_sinh = $data['ngay_sinh'];
        $so_dien_thoai = $data['so_dien_thoai'];
        $email = $data['email'];
        $query = "UPDATE doc_gia SET ma_doc_gia =:ma_doc_gia, ten_doc_gia=: ten_doc_gia, ngay_sinh=:ngay_sinh, so_dien_thoai=:so_dien_thoai, email=:email WHERE ma_doc_gia=: $id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_doc_gia', $ma_doc_gia);
        $stmt->bindParam(':ten_doc_gia', $ten_doc_gia);
        $stmt->bindParam(':ngay_sinh', $ngay_sinh);
        $stmt->bindParam(':so_dien_thoai', $so_dien_thoai);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();
    }

    public function addReader($data)
    {
        $query = "INSERT INTO {$this->table} (ten_doc_gia,ngay_sinh, so_dien_thoai, email) 
                  VALUES (:ten_doc_gia, :ngay_sinh, :so_dien_thoai, :email )";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'ten_doc_gia' => $data['ten_doc_gia'],
            'ngay_sinh' => $data['ngay_sinh'],
            'so_dien_thoai' => $data['so_dien_thoai'],
            'email' => $data['email'],
        ]);
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

    // public function getTotalReaders()
    // {
    //     $query = "SELECT COUNT(*) AS total FROM {$this->table}";
    //     $stmt = $this->db->prepare($query);
    //     $stmt->execute();
    //     return $stmt->fetchColumn();
    // }

    // public function getReadersWithPagination($start, $limit)
    // {
    //     $query = "SELECT * FROM {$this->table} LIMIT :start, :limit";
    //     $stmt = $this->db->prepare($query);
    //     $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    //     $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }
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
}
