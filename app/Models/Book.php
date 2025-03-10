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
        $sql = "select * from sach as s join tac_gia as tg on tg.ma_tac_gia = s.ma_tac_gia join the_loai as tl on tl.ma_the_loai = s.ma_the_loai where s.ma_sach = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}