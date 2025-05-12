<?php
// This class uses PDO para po mas secure ang database interactions

class Database{
    private $host = "localhost";
    private $username = "root";
    private $password = "Taurus0425";
    private $database ="library";
    private $conn;
    public function __construct(){
        //try-catch, error handling
        try{
            //establisihing a database connection Data Source Name naglalaman ng database type, host, database name, and character set
            $db= "mysql:host=$this->host;dbname=$this->database;charset=utf8mb4";
            //configurer ang behavior ng PDO
            $options=[
                //PDO Options, sets error to throws exception, error handling
                PDO::ATTR_ERRMODE => PDO:: ERRMODE_EXCEPTION, 
                //sets fetch mode returns the result as an associative array 
                PDO::ATTR_DEFAULT_FETCH_MODE=> PDO::FETCH_ASSOC, 
                //ino-off persistent connection
                PDO::ATTR_PERSISTENT=> false
            ];
            //PDO instance which has dsn, username, password and options
            $this->conn = new PDO($db, $this->username, $this->password, $options);

        }catch(PDOException $e){
            die ("Connection Failed: ". $e->getMessage());

        }
    }
    
    //this method provides access to the PDO database connection
    public function getConnection(){
        return $this->conn;
    }

    public function setConnection($conn){
        $this->conn = $conn;
    }
    public function __destruct(){
        $this-> conn = null;
    }

}