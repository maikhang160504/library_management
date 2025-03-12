<?php
namespace App\Models;

use PDO;
use App\Core\Model;
use Exception;
use PDOException;

class Auth
{
   
     public function validateUser($username, $password)
     {
         $fixedUsername = "Quockiet@123";
         $fixedPassword = "Quockiet@123"; 
     
         if ($username === $fixedUsername && $password === $fixedPassword) {
             $_SESSION['user'] = $username; // Lưu username vào session
             return true;
         }
         return false;
     }
     
}
