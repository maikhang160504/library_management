<?php
namespace App\Models;
use PDO;
use PDOException;
use App\Core\Model;
class Reader extends Model {
    protected $table = 'doc_gia';

    // Lấy tất cả độc giả
    public function getAllReaders() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}