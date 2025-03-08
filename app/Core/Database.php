<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private $connection;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $dbConfig = $config['database'];

        try {
            $this->connection = new PDO(
                "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}",
                $dbConfig['username'],
                $dbConfig['password']
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}