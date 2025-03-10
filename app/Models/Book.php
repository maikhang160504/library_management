<?php
namespace App\Models;
use PDO;
use App\Core\Model;

class Book extends Model
{  
     protected $table = 'sach';
    
     public function getAllBooks() {
        $query = "
            SELECT 
                s.*, 
                tl.ten_the_loai,
                tg.ten_tac_gia
            FROM {$this->table} s
            LEFT JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
            LEFT JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookById($id)
    {
        $sql = "select * from {$this->table} as s join tac_gia as tg on tg.ma_tac_gia = s.ma_tac_gia join the_loai as tl on tl.ma_the_loai = s.ma_the_loai where s.ma_sach = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addBook($data) {
        try {
            $sql = "CALL ThemSach(:ten_sach, :ten_tac_gia, :ten_the_loai, :nam_xuat_ban, :nha_xuat_ban, :so_luong)";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                'ten_sach' => $data['ten_sach'],
                'ten_tac_gia' => $data['ten_tac_gia'],
                'ten_the_loai' => $data['ten_the_loai'],
                'nam_xuat_ban' => $data['nam_xuat_ban'],
                'nha_xuat_ban' => $data['nha_xuat_ban'],
                'so_luong' => $data['so_luong']
            ]);
        } catch (\PDOException $e) {
            // Log lỗi nếu cần
            return false;
        }
    }

    public function getBooksPaginated($limit, $offset)
    {
        $sql = "SELECT 
                    s.ma_sach,
                    s.ten_sach,
                    tg.ten_tac_gia,
                    tl.ten_the_loai,
                    s.so_luong
                FROM sach s 
                INNER JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
                INNER JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countBooks()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function updateQuantity($ma_sach, $so_luong) {
        $sql = "UPDATE {$this->table} SET so_luong = :so_luong WHERE ma_sach = :ma_sach";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'so_luong' => $so_luong,
            'ma_sach' => $ma_sach
        ]);
    }
}