<?php 

namespace App\Core;

use PDO;
use PDOException;

class Database 
{
    private static $instance = null;

    private static function getConnexion()
    {
        if(self::$instance === null) {

            try {
                $host = $_ENV['DB_HOST'];
                $db   = $_ENV['DB_NAME'];
                $user = $_ENV['DB_USER'];
                $pass = $_ENV['DB_PASS'];

                $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

                self::$instance = new PDO($dsn, $user, $pass);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }   

        return self::$instance;
    }  
    public static function getInstance()
    {
        return self::getConnexion();
    }
}