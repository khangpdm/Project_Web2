<?php
    class Database{
        private $db_host = "localhost";
        private $db_user = "root";
        private $db_name = "treeshop";
        private $db_pass = "";
        private $db_port = 3307;
        private $conn;

        public function __construct(){
            try{
                $this->conn = mysqli_connect($this->db_host,$this->db_user,$this->db_pass,$this->db_name,$this->db_port);
            }
            catch (mysqli_sql_exception $e) {
                die("Error connecting to database: " . $e->getMessage());
            }            
        }

        public function getConnection(){
            return $this->conn;
        }
        public function closeConnection(){
            mysqli_close($this->conn);
        }
    }