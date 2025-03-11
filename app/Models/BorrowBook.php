<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Core\Model;

class BorrowBook extends Model
{
    protected $table = 'phieu_muon';

    public function createBorrow($ma_doc_gia, $ngay_muon, $ngay_tra, $danh_sach_sach)
    {
        $this->db->beginTransaction();
        try {
            // Thêm phiếu mượn
            $query = "INSERT INTO {$this->table} (ma_doc_gia, ngay_muon, ngay_tra, trang_thai) 
                      VALUES (:ma_doc_gia, :ngay_muon, :ngay_tra, 'Đang mượn')";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':ma_doc_gia', $ma_doc_gia);
            $stmt->bindParam(':ngay_muon', $ngay_muon);
            $stmt->bindParam(':ngay_tra', $ngay_tra);
            $stmt->execute();
            $ma_phieu_muon = $this->db->lastInsertId();

            // Thêm chi tiết phiếu mượn
            foreach ($danh_sach_sach as $sach) {
                $query = "INSERT INTO chi_tiet_phieu_muon (ma_phieu_muon, ma_sach, so_luong) 
                          VALUES (:ma_phieu_muon, :ma_sach, :so_luong)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':ma_phieu_muon', $ma_phieu_muon);
                $stmt->bindParam(':ma_sach', $sach['ma_sach']);
                $stmt->bindParam(':so_luong', $sach['so_luong']);
                $stmt->execute();

                // Giảm số lượng sách hiện có
                $query = "UPDATE sach SET so_luong = so_luong - :so_luong WHERE ma_sach = :ma_sach";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':so_luong', $sach['so_luong']);
                $stmt->bindParam(':ma_sach', $sach['ma_sach']);
                $stmt->execute();
            }

            $this->db->commit();
            return ['ma_phieu_muon' => $ma_phieu_muon, 'success' => true];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }


    // Lấy danh sách phiếu mượn chưa trả
    public function getUnreturnedBorrows()
    {
        $query = "CALL LayPhieuMuonChuaTra()";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getallBorrows()
    {
        $query = "SELECT pm.ma_phieu_muon, pm.ma_doc_gia,dg.ten_doc_gia, pm.ngay_muon, pm.ngay_tra, pm.trang_thai,
                    MIN(ctpm.ma_ctpm) AS ma_ctpm 
                    FROM phieu_muon pm
                    JOIN chi_tiet_phieu_muon ctpm ON pm.ma_phieu_muon = ctpm.ma_phieu_muon
                    join doc_gia dg on pm.ma_doc_gia = dg.ma_doc_gia
                    GROUP BY pm.ma_phieu_muon, pm.ma_doc_gia, pm.ngay_muon, pm.ngay_tra, pm.trang_thai
                    ORDER BY pm.ma_phieu_muon DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBorrowStats($filter)
    {
        $query = "CALL GetBorrowStats(:filter)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":filter", $filter);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTotalBorrows($filter)
    {
        $query = "SELECT total_borrows(:filter) AS total";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":filter", $filter, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getYearlyReaderStats()
    {
        $query = "CALL ThongKeDocGiaMuonNam()";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getYearlyReaderStatsDetail()
    {
        $query = "SELECT 
                    dg.ma_doc_gia, 
                    dg.ten_doc_gia, 
                    COUNT(pm.ma_phieu_muon) AS so_lan_muon,
                    get_top_genre_by_reader(dg.ma_doc_gia) AS the_loai_muon_nhieu_nhat
                  FROM phieu_muon pm
                  JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
                  WHERE YEAR(pm.ngay_muon) = YEAR(CURRENT_DATE)
                  GROUP BY dg.ma_doc_gia, dg.ten_doc_gia
                  ORDER BY so_lan_muon DESC;";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function checkBookQuantity($ma_sach)
    {
        $query = "SELECT KiemTraSoLuongSach(:ma_sach) AS so_luong_con";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_sach', $ma_sach);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['so_luong_con'];
    }
    public function getMostBorrowedBooks($startDate, $endDate)
    {
        $query = "CALL ThongKeSachMuonNhieuNhat(:startDate, :endDate)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTopReaders($startDate, $endDate)
    {
        $query = "CALL ThongKeDocGiaMuonNhieuNhat(:startDate, :endDate)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBorrowReturnReport($thang, $nam)
    {
        $query = "CALL XuatBaoCaoMuonTra(:thang, :nam)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':thang', $thang);
        $stmt->bindParam(':nam', $nam);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBorrowDetail($ma_phieu_muon)
    {
        // Lấy thông tin phiếu mượn
        $query = "SELECT pm.*, dg.ten_doc_gia, dg.so_dien_thoai 
                  FROM phieu_muon pm
                  JOIN doc_gia dg ON pm.ma_doc_gia = dg.ma_doc_gia
                  WHERE pm.ma_phieu_muon = :ma_phieu_muon";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_phieu_muon', $ma_phieu_muon);
        $stmt->execute();
        $borrowInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$borrowInfo) {
            return null; // Không tìm thấy phiếu mượn
        }

        // Lấy danh sách sách trong phiếu mượn
        $query = "SELECT ctpm.*, s.ten_sach , tg.ten_tac_gia
                  FROM chi_tiet_phieu_muon ctpm
                  JOIN sach s ON ctpm.ma_sach = s.ma_sach
                  join tac_gia tg on tg.ma_tac_gia = s.ma_tac_gia
                  WHERE ctpm.ma_phieu_muon = :ma_phieu_muon";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_phieu_muon', $ma_phieu_muon);
        $stmt->execute();
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Kết hợp thông tin
        $borrowInfo['books'] = $books;
        return $borrowInfo;
    }
    function getUpcomingReturns($days = 3)
    {
        $query = "CALL GetUpcomingReturns(:days)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':days', $days);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBorrowsbyStatusandTenDocGia($trang_thai, $ten_doc_gia)
    {
        $status = '';
        if ($trang_thai == 'all') {
            $status = ' 1 = 1 ';
        } else {
            $status = ' pm.trang_thai = :trang_thai ';
        }
        $query = "SELECT pm.ma_phieu_muon, pm.ma_doc_gia,dg.ten_doc_gia, pm.ngay_muon, pm.ngay_tra, pm.trang_thai,
                    MIN(ctpm.ma_ctpm) AS ma_ctpm 
                    FROM phieu_muon pm
                    JOIN chi_tiet_phieu_muon ctpm ON pm.ma_phieu_muon = ctpm.ma_phieu_muon
                    join doc_gia dg on pm.ma_doc_gia = dg.ma_doc_gia
                    WHERE $status
                    and dg.ten_doc_gia like '%$ten_doc_gia%'
                    GROUP BY pm.ma_phieu_muon, pm.ma_doc_gia, pm.ngay_muon, pm.ngay_tra, pm.trang_thai
                    ORDER BY pm.ma_phieu_muon DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':trang_thai', $trang_thai);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getLeastBorrowedBooks($limit = 10)
    {
        $query = "
            SELECT s.ma_sach, s.ten_sach, t.ten_tac_gia, COUNT(pm.ma_phieu_muon) AS so_luot_muon
            FROM sach s
            LEFT JOIN chi_tiet_phieu_muon ctp ON s.ma_sach = ctp.ma_sach
            LEFT JOIN phieu_muon pm ON ctp.ma_phieu_muon = pm.ma_phieu_muon
            LEFT JOIN tac_gia t ON s.ma_tac_gia = t.ma_tac_gia
            GROUP BY s.ma_sach, s.ten_sach, t.ten_tac_gia
            ORDER BY so_luot_muon ASC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBlackList()
    {
        $query = "SELECT dg.ma_doc_gia, dg.ten_doc_gia, COUNT(pt.ma_ctpm) AS so_lan_bi_phat, tinh_tong_tien_phat(dg.ma_doc_gia) AS tong_tien_phat
                    FROM doc_gia dg
                    JOIN phieu_muon pm ON dg.ma_doc_gia = pm.ma_doc_gia
                    JOIN chi_tiet_phieu_muon ctpm ON pm.ma_phieu_muon = ctpm.ma_phieu_muon
                    JOIN phieu_tra pt ON ctpm.ma_ctpm = pt.ma_ctpm
                    GROUP BY dg.ma_doc_gia, dg.ten_doc_gia
                    HAVING COUNT(pt.ma_ctpm) >= 3 OR tinh_tong_tien_phat(dg.ma_doc_gia) > 400000
                    ORDER BY tong_tien_phat DESC;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
