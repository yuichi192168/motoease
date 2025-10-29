<?php
if(!defined('DB_SERVER')){
    require_once("../initialize.php"); // keep if project expects it
}

class DBConnection {
    private $host = "localhost";
    private $username = "root";
    private $password = "";           // XAMPP default = empty string
    private $database = "motoease_db";
    private $port = 3306;
    private $host = DB_SERVER;
    private $username = DB_USERNAME;
    
    private $password = DB_PASSWORD;
    private $database = DB_NAME;
    private $port = DB_PORT;

    
//     private $password = DB_PASSWORD;
//    private $database = DB_NAME;    
//     private $port = DB_PORT;
//     private $port = 3307; 

    public $conn;

    public function __construct(){
        if (!isset($this->conn)) {
            // try 3306
            $this->conn = @new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

            // if failed, try 3307 (some XAMPP setups use 3307)
            if ($this->conn->connect_errno) {
                $this->port = 3307;
                $this->conn = @new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
            }

            if ($this->conn->connect_errno) {
                // show clear error and stop
                die("Cannot connect to database server: " . $this->conn->connect_error);
            }
        }
    }

    public function __destruct(){
        // only close if object exists and connection is alive
        if ($this->conn instanceof mysqli) {
            if (@$this->conn->ping()) {
                $this->conn->close();
            }
        }
    }
}
?>
