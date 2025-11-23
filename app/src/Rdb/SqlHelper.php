<?php

namespace App\Rdb;

use mysqli;
use RuntimeException;

// SqlHelper - класс для работы с SQL-подключениями
class SqlHelper {
    private static ?mysqli $connection = null;  

    public function __construct() {
        $this->pingDb();
    }

    // pingDb 
    private function pingDb(): void {
        $connection = $this->openDbConnection();
        if ($connection->ping()) {
            $connection->close();
        } else {
            throw new RuntimeException("Database is not available");
        }
    }

    // openDbConnection 
    public function openDbConnection(): mysqli {
        $host = $_ENV["DB_HOST"] ?? "localhost";  
        $user = $_ENV["DB_USERNAME"] ?? "root";
        $password = $_ENV["DB_PASSWORD"] ?? "";
        $database = $_ENV["DB_NAME"] ?? "country_db";
        $port = $_ENV["DB_PORT"] ?? 3306;  

        $connection = new mysqli(
            hostname: $host,
            port: $port, 
            username: $user, 
            password: $password, 
            database: $database, 
        );

        if ($connection->connect_errno) {
            throw new RuntimeException("Не удалось подключиться к MySQL: " . $connection->connect_error);
        }

        $connection->set_charset('utf8mb4'); 

        return $connection;
    }

    public function getConnection(): mysqli
    {
        if (self::$connection === null || !self::$connection->ping()) {
            self::$connection = $this->openDbConnection();
        }
        return self::$connection;
    }

    public static function closeConnection(): void
    {
        if (self::$connection !== null) {
            self::$connection->close();
            self::$connection = null;
        }
    }
}
