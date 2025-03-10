<?php
namespace App\Models;
use PDO;
use PDOException;
use App\Core\Model;

class Penalty extends Model {
    protected $table = 'phi_phat';

    public function getAllPenalties() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($query); 
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
