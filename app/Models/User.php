<?php
namespace App\Models;

use App\Core\Model;
use PDO;
class User extends Model
{
    public function getUserByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM doc_gia WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}