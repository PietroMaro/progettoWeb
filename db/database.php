<?php
class Database
{
    private static $instance = null;
    private $connection;

    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "unisell";

    private function __construct()
    {
        // Enable exception reporting for mysqli
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $this->connection = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        } catch (Exception $e) {
            // We catch the critical mysqli error and THROW a generic Exception.
            // This allows index.php to catch it and show the red modal instead of crashing.
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        // FIX: Only create a new instance if one doesn't exist yet
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
?>