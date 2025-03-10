<?php
namespace App\Models;

use PDO;
use App\Core\Model;

class Penalty extends Model {

    public function getAllPenalties() {
        $query = "
            SELECT 
                dg.ma_doc_gia,
                dg.ten_doc_gia,
                pt.ngay_tra_sach,
                pt.tien_phat
            FROM phieu_tra pt
            LEFT JOIN chi_tiet_phieu_muon ctpm ON pt.ma_ctpm = ctpm.ma_ctpm
            LEFT JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
            LEFT JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
            WHERE pt.tien_phat > 0
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

