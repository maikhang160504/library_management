<?php
namespace App\Models;
use PDO;
use App\Core\Model;

class Borrow extends Model
{
    public function getAllBorrows()
    {
        $stmt = $this->db->query("SELECT * FROM phieu_muon");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}