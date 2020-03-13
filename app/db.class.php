<?php
class ConnectDb {
    // Hold the class instance.
    private static $instance = null;
    private $conn;

    private $host = 'localhost';
    private $user = 'root';
    private $pass = 'root';
    private $name = 'burger';

    // The db connection is established in the private constructor.
    private function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host={$this->host};
    dbname={$this->name}", $this->user,$this->pass,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new ConnectDb();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}