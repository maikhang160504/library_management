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
        return $stmt->execute(); // Trả về true/false
    }

    public function updateReader($id, $data)
    {
        $ten_doc_gia = $data['ten_doc_gia'];
        $ngay_sinh = $data['ngay_sinh'];
        $so_dien_thoai = $data['so_dien_thoai'];
        $email = $data['email'];
        $query = "UPDATE doc_gia SET ma_doc_gia =:ma_doc_gia, ten_doc_gia=: ten_doc_gia, ngay_sinh=:ngay_sinh, so_dien_thoai=:so_dien_thoai, email=:email WHERE ma_doc_gia=: $id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_doc_gia', $ma_doc_giaame);
        $stmt->bindParam(':ten_doc_gia', $ten_doc_gia);
        $stmt->bindParam(':ngay_sinh', $ngay_sinh);
        $stmt->bindParam(':so_dien_thoai', $so_dien_thoai);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();
    }

    public function addReader($data) {
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

    // Chi tiết độc giả và lịch sử mượn sách
    public function detailReader($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE ma_doc_gia = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $reader = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reader) {
            return null; // Trả về null nếu không tìm thấy độc giả
        }

        // Lấy lịch sử mượn sách của độc giả
        $query = "SELECT pm.ma_phieu_muon, s.ten_sach, pm.ngay_muon, pm.ngay_tra, pt.tien_phat
                  FROM chi_tiet_phieu_muon ctpm
                  JOIN sach s ON ctpm.ma_sach = s.ma_sach
                  JOIN phieu_muon pm ON pm.ma_phieu_muon = ctpm.ma_phieu_muon
                  JOIN phieu_tra pt ON pt.ma_ctpm = ctpm.ma_ctpm
                  WHERE pm.ma_doc_gia = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $borrowHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'reader' => $reader,
            'borrowHistory' => $borrowHistory
        ];
    }
}
