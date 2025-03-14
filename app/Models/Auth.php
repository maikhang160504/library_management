<?php

namespace App\Models;

use PDO;
use App\Core\Model;
use Exception;
use PDOException;
use Dotenv\Dotenv;


    class Auth
    {
        public function __construct()
        {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); 
            $dotenv->load();
        }

        public function validateUser($username, $password)
        {
            if (!isset($_ENV['USERS'])) {
                return false;
            }
        
            $usersRaw = explode(',', $_ENV['USERS']); // Tách danh sách user từ .env
            $users = array_map(function ($user) {
                $parts = explode('|', $user);
                if (count($parts) !== 3) return null; // Bỏ qua dòng lỗi
                return ['fullname' => trim($parts[0]), 'username' => trim($parts[1]), 'pass' => trim($parts[2])];
            }, $usersRaw);
        
            // Loại bỏ null nếu có lỗi trong .env
            $users = array_filter($users);
        
            foreach ($users as $user) {
                if ($user['username'] === $username && $user['pass'] === $password) {
                    $_SESSION['user'] = [  
                        'username' => $user['username'],
                        'fullname' => $user['fullname']  
                    ];
                    return true;
                }
            }
        
            return false;
        }
        
    }

