<?php
namespace App\Models;

use App\Core\Model;

class ReturnBook extends Model {
    public function getAllReturns() {
        $stmt = $this->db->prepare("SELECT * FROM returns");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
