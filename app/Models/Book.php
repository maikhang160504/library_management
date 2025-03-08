<?php
namespace App\Models;
use PDO;
use App\Core\Model;

class Book extends Model
{
    public function getAllBooks()
    {
        $stmt = $this->db->query("SELECT * FROM sach");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM sach WHERE ma_sach = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}