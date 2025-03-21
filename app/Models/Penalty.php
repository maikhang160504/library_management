<?php

namespace App\Models;

use PDO;
use App\Core\Model;

class Penalty extends Model
{

    public function getAllPenalties($keyword = null)
    {
        $query = "
            SELECT 
                pm.ma_phieu_muon,
                dg.ma_doc_gia,
                dg.ten_doc_gia,
                pm.ngay_tra AS ngay_het_han,  -- Ngày hết hạn 
                pt.ngay_tra_sach,             -- Ngày thực tế 
                pt.tien_phat,                 -- Số tiền phạt 
                pm.trang_thai                 -- Trạng thái (Đang mượn, Đã trả)
            FROM phieu_tra pt
            LEFT JOIN chi_tiet_phieu_muon ctpm ON pt.ma_ctpm = ctpm.ma_ctpm
            LEFT JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
            LEFT JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
            WHERE pt.tien_phat > 0
        ";

        if ($keyword) {
            $query .= " AND (dg.ma_doc_gia LIKE :keyword OR dg.ten_doc_gia LIKE :keyword OR pt.tien_phat LIKE :keyword)";
        }
        $stmt = $this->db->prepare($query);

        // Nếu có từ khóa tìm kiếm, gán giá trị vào tham số
        if ($keyword) {
            $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPenaltiesByDate($date_filter = null)
    {
        $query = "
            SELECT 
                pm.ma_phieu_muon,
                pt.ngay_tra_sach,             
                dg.ma_doc_gia,
                dg.ten_doc_gia,
                pm.ngay_tra AS ngay_het_han,  
                pt.tien_phat,                 
                pm.trang_thai                 
            FROM phieu_tra pt
            LEFT JOIN chi_tiet_phieu_muon ctpm ON pt.ma_ctpm = ctpm.ma_ctpm
            LEFT JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
            LEFT JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
            WHERE pt.tien_phat > 0 ";

        // Thêm điều kiện lọc theo thời gian
        if ($date_filter) {
            if ($date_filter == 'today') { 
                $query .= " AND DATE(pt.ngay_tra_sach) = CURRENT_DATE()"; 
            } elseif ($date_filter == 'this_week') { 
                $query .= " AND YEARWEEK(pt.ngay_tra_sach, 1) = YEARWEEK(CURRENT_DATE(), 1)";
            } elseif ($date_filter == 'this_month') { 
                $query .= " AND MONTH(pt.ngay_tra_sach) = MONTH(CURRENT_DATE()) 
                            AND YEAR(pt.ngay_tra_sach) = YEAR(CURRENT_DATE())";
            } elseif ($date_filter == 'last_month') { 
                $query .= " AND pt.ngay_tra_sach >= LAST_DAY(DATE_SUB(CURRENT_DATE(), INTERVAL 2 MONTH)) + INTERVAL 1 DAY
                            AND pt.ngay_tra_sach <= LAST_DAY(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))";
            } elseif ($date_filter == 'this_year') { 
                $query .= " AND YEAR(pt.ngay_tra_sach) = YEAR(CURRENT_DATE())";
            } elseif ($date_filter == 'last_year') { 
                $query .= " AND YEAR(pt.ngay_tra_sach) = YEAR(CURRENT_DATE()) - 1";
            }
        }

        $stmt = $this->db->prepare($query);

        $stmt->execute();
        $penalties = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $penalties ?: [];
    }
    public function checkPenalty($ma_muon_sach) {
        $query = "CALL CheckPenalty(:ma_muon_sach, @penaltyStatus)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_muon_sach', $ma_muon_sach, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor(); 
    
        // Lấy giá trị biến @penaltyStatus
        $query = "SELECT @penaltyStatus AS 'check'";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function searchPenalties($search)
{
    $sql = "
        SELECT 
            pm.ma_phieu_muon,
            dg.ma_doc_gia,
            dg.ten_doc_gia,
            pm.ngay_tra AS ngay_het_han,  
            pt.ngay_tra_sach,             
            pt.tien_phat,                 
            pm.trang_thai                 
        FROM phieu_tra pt
        LEFT JOIN chi_tiet_phieu_muon ctpm ON pt.ma_ctpm = ctpm.ma_ctpm
        LEFT JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
        LEFT JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
        WHERE pt.tien_phat > 0 
        AND (pm.ma_phieu_muon LIKE :keyword OR dg.ten_doc_gia LIKE :keyword)
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':keyword', '%' . $search . '%', PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


//     public function getPenaltiesWithPagination($start, $perPage, $keyword = '')
// {
//     $query = "
//         SELECT 
//             pt.ma_phieu_tra,
//             pm.ma_phieu_muon,
//             dg.ma_doc_gia,
//             dg.ten_doc_gia,
//             pm.ngay_tra AS ngay_het_han,  
//             pt.ngay_tra_sach,             
//             pt.tien_phat,                 
//             pm.trang_thai                 
//         FROM phieu_tra pt
//         LEFT JOIN chi_tiet_phieu_muon ctpm ON pt.ma_ctpm = ctpm.ma_ctpm
//         LEFT JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
//         LEFT JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
//         WHERE pt.tien_phat > 0
//     ";

//     if ($keyword) {
//         $query .= " AND (pm.ma_phieu_muon LIKE :keyword OR dg.ten_doc_gia LIKE :keyword)";
//     }

//     $query .= " LIMIT :start, :perPage";

//     $stmt = $this->db->prepare($query);
    
//     if ($keyword) {
//         $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
//     }

//     $stmt->bindValue(':start', $start, PDO::PARAM_INT);
//     $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
//     $stmt->execute();
    
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
// }

public function getPenaltiesWithPagination($start, $perPage, $keyword = '')
{
    $query = "
        SELECT DISTINCT pm.ma_phieu_muon
        FROM phieu_tra pt
        LEFT JOIN chi_tiet_phieu_muon ctpm ON pt.ma_ctpm = ctpm.ma_ctpm
        LEFT JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
        LEFT JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
        WHERE pt.tien_phat > 0
    ";

    if ($keyword) {
        $query .= " AND (pm.ma_phieu_muon LIKE :keyword OR dg.ten_doc_gia LIKE :keyword)";
    }

    $query .= " ORDER BY pm.ma_phieu_muon DESC LIMIT :start, :perPage";

    $stmt = $this->db->prepare($query);
    if ($keyword) {
        $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', (int)$perPage, PDO::PARAM_INT);
    $stmt->execute();

    $maPhieuMuonList = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($maPhieuMuonList)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($maPhieuMuonList), '?'));

    $query = "
        SELECT 
            pt.ma_phieu_tra,
            pm.ma_phieu_muon,
            dg.ma_doc_gia,
            dg.ten_doc_gia,
            pm.ngay_tra AS ngay_het_han,  
            pt.ngay_tra_sach,             
            pt.tien_phat,                 
            pm.trang_thai                 
        FROM phieu_tra pt
        LEFT JOIN chi_tiet_phieu_muon ctpm ON pt.ma_ctpm = ctpm.ma_ctpm
        LEFT JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
        LEFT JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
        WHERE pm.ma_phieu_muon IN ($placeholders)
        ORDER BY pm.ma_phieu_muon DESC
    ";

    $stmt = $this->db->prepare($query);
    $stmt->execute($maPhieuMuonList);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getTotalPenalties($keyword = '')
    {
        if ($keyword) {
            $query = "
            SELECT COUNT(*) 
            FROM phieu_tra pt
            LEFT JOIN chi_tiet_phieu_muon ctpm ON pt.ma_ctpm = ctpm.ma_ctpm
            LEFT JOIN phieu_muon pm ON ctpm.ma_phieu_muon = pm.ma_phieu_muon
            LEFT JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
            WHERE dg.ten_doc_gia LIKE :keyword OR pm.ma_phieu_muon LIKE :keyword
        ";
        

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
