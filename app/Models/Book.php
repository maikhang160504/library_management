<?php
namespace App\Models;

use PDO;
use App\Core\Model;
use Exception;
use PDOException;
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
    $sql = "SELECT 
                s.ma_sach,
                s.ma_tac_gia,       -- Nếu cần cập nhật tác giả, phải SELECT cột này
                s.ma_the_loai,      -- Nếu cần cập nhật thể loại, cũng SELECT cột này
                s.ten_sach,
                s.nam_xuat_ban,
                s.nha_xuat_ban,
                s.so_luong,
                tg.ten_tac_gia,
                tl.ten_the_loai
            FROM sach s
            INNER JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
            INNER JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
            WHERE s.ma_sach = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC); // Nếu không có dòng nào, fetch() trả về false
}

public function addBook($data)
{
    try {
        $sql = "CALL ThemSach(
            :ten_sach,
            :ten_tac_gia,
            :ten_the_loai,
            :nam_xuat_ban,
            :nha_xuat_ban,
            :so_luong,
            :ngay_them
        )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'ten_sach'      => $data['ten_sach'],
            'ten_tac_gia'   => $data['ten_tac_gia'],
            'ten_the_loai'  => $data['ten_the_loai'],
            'nam_xuat_ban'  => $data['nam_xuat_ban'],
            'nha_xuat_ban'  => $data['nha_xuat_ban'],
            'so_luong'      => $data['so_luong'],
            'ngay_them'     => $data['ngay_them']
        ]);
    } catch (\PDOException $e) {
        error_log($e->getMessage()); // log lỗi cho debug
        return false;
    }
}


public function updateBook($data)
{
    try {
        $sql = "CALL CapNhat(:ma_sach, :ten_sach, :nam_xuat_ban, :nha_xuat_ban, :so_luong, :ten_tac_gia, :ten_the_loai)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':ma_sach', $data['ma_sach'], PDO::PARAM_INT);
        $stmt->bindParam(':ten_sach', $data['ten_sach'], PDO::PARAM_STR);
        $stmt->bindParam(':nam_xuat_ban', $data['nam_xuat_ban'], PDO::PARAM_INT);
        $stmt->bindParam(':nha_xuat_ban', $data['nha_xuat_ban'], PDO::PARAM_STR);
        $stmt->bindParam(':so_luong', $data['so_luong'], PDO::PARAM_INT);
        $stmt->bindParam(':ten_tac_gia', $data['ten_tac_gia'], PDO::PARAM_STR);
        $stmt->bindParam(':ten_the_loai', $data['ten_the_loai'], PDO::PARAM_STR);

        return $stmt->execute();

    } catch (PDOException $e) {
        error_log($e->getMessage());
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
public function getBooksPaging($limit, $offset)
{
    $sql = "
        SELECT 
            s.*, 
            tl.ten_the_loai,
            tg.ten_tac_gia
        FROM {$this->table} s
        LEFT JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
        LEFT JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function countAllBooks()
{
    $sql = "SELECT COUNT(*) as total FROM {$this->table}";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

public function searchBooksPaging($query, $limit, $offset)
{
    $sql = "SELECT s.*, tg.ten_tac_gia, tl.ten_the_loai
            FROM sach s
            LEFT JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
            LEFT JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
            WHERE s.ten_sach LIKE :query
            OR tg.ten_tac_gia LIKE :query
            LIMIT :limit OFFSET :offset";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function countSearch($query)
{
    $sql = "SELECT COUNT(*) as total
            FROM sach s
            LEFT JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
            WHERE s.ten_sach LIKE :query
            OR tg.ten_tac_gia LIKE :query";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['query' => "%$query%"]);

    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

public function getBooksByCategoryPaging($categoryId, $limit, $offset)
{
    $sql = "SELECT s.*, tg.ten_tac_gia, tl.ten_the_loai
            FROM sach s
            LEFT JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
            LEFT JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
            WHERE s.ma_the_loai = :categoryId
            LIMIT :limit OFFSET :offset";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function countBooksByCategory($categoryId)
{
    $sql = "SELECT COUNT(*) as total
            FROM sach
            WHERE ma_the_loai = :categoryId";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

public function searchBooksInCategoryPaging($query, $categoryId, $limit, $offset)
{
    $sql = "SELECT s.*, tg.ten_tac_gia, tl.ten_the_loai
            FROM sach s
            LEFT JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
            LEFT JOIN the_loai tl ON s.ma_the_loai = tl.ma_the_loai
            WHERE s.ma_the_loai = :categoryId
            AND (s.ten_sach LIKE :query OR tg.ten_tac_gia LIKE :query)
            LIMIT :limit OFFSET :offset";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function countSearchInCategory($query, $categoryId)
{
    $sql = "SELECT COUNT(*) as total
            FROM sach s
            LEFT JOIN tac_gia tg ON s.ma_tac_gia = tg.ma_tac_gia
            WHERE s.ma_the_loai = :categoryId
            AND (s.ten_sach LIKE :query OR tg.ten_tac_gia LIKE :query)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);

    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

public function getStatisticsByDay() {
    $sql = "CALL ThongKeChiTietTheoNgay()";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getStatisticsByMonth() {
    $sql = "CALL ThongKeChiTietTheoThang()";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getStatisticsByYear() {
    $sql = "CALL ThongKeChiTietTheoNam()";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
