<?php
namespace App\Models;

use PDO;
use App\Core\Model;
use Exception;

class Book extends Model
{
    protected $table = 'sach';

    public function getAllBooks()
    {
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
        $sql = "SELECT * FROM {$this->table} s 
                JOIN tac_gia tg ON tg.ma_tac_gia = s.ma_tac_gia 
                JOIN the_loai tl ON tl.ma_the_loai = s.ma_the_loai 
                WHERE s.ma_sach = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addBook($data)
    {
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
            return false;
        }
    }

    public function updateBook($data)
    {
        try {
            $this->db->beginTransaction();

            $sqlBook = "UPDATE {$this->table} 
                        SET ten_sach = :ten_sach, 
                            nam_xuat_ban = :nam_xuat_ban, 
                            nha_xuat_ban = :nha_xuat_ban, 
                            so_luong = :so_luong 
                        WHERE ma_sach = :ma_sach";
            $stmtBook = $this->db->prepare($sqlBook);
            $stmtBook->execute([
                'ten_sach'     => $data['ten_sach'],
                'nam_xuat_ban' => $data['nam_xuat_ban'],
                'nha_xuat_ban' => $data['nha_xuat_ban'],
                'so_luong'     => $data['so_luong'],
                'ma_sach'      => $data['ma_sach']
            ]);

            $book = $this->getBookById($data['ma_sach']);
            if (!$book) {
                throw new Exception("Không tìm thấy sách với mã: " . $data['ma_sach']);
            }

            $sqlAuthor = "UPDATE tac_gia 
                          SET ten_tac_gia = :ten_tac_gia 
                          WHERE ma_tac_gia = :ma_tac_gia";
            $stmtAuthor = $this->db->prepare($sqlAuthor);
            $stmtAuthor->execute([
                'ten_tac_gia' => $data['ten_tac_gia'],
                'ma_tac_gia'  => $book['ma_tac_gia']
            ]);

            $sqlCategory = "UPDATE the_loai 
                            SET ten_the_loai = :ten_the_loai 
                            WHERE ma_the_loai = :ma_the_loai";
            $stmtCategory = $this->db->prepare($sqlCategory);
            $stmtCategory->execute([
                'ten_the_loai' => $data['ten_the_loai'],
                'ma_the_loai'  => $book['ma_the_loai']
            ]);

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getCategories()
    {
        $sql = "SELECT * FROM the_loai";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBooksByCategory($ma_the_loai)
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
                WHERE s.ma_the_loai = :ma_the_loai";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ma_the_loai' => $ma_the_loai]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchBooks($query)
    {
        $sql = "SELECT s.*, tg.ten_tac_gia, tl.ten_the_loai
                FROM sach s
                LEFT JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
                LEFT JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
                WHERE s.ten_sach LIKE :query
                OR tg.ten_tac_gia LIKE :query";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'query' => "%$query%"
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchBooksInCategory($query, $ma_the_loai)
{
    $sql = "SELECT s.*, tg.ten_tac_gia, tl.ten_the_loai
            FROM sach s
            LEFT JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
            LEFT JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
            WHERE s.ma_the_loai = :ma_the_loai
            AND (s.ten_sach LIKE :query OR tg.ten_tac_gia LIKE :query)";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        'ma_the_loai' => $ma_the_loai,
        'query' => "%$query%"
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
