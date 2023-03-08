<?php

//databaseHandler
class Dbh {

    private $host = "localhost";
    private $db_user = "root";
    private $db_password = "";
    private $db_name = "poker";

    public function connect(){
        try{
            $dbh = New PDO('mysql:host=localhost;dbname=poker',$this->db_user,$this->db_password);
            return $dbh;
        }catch(PDOException $e){
            echo "Error: ".$e->getMessage()."<br>";
            die();
        }
    }

    protected function mySqliConnect(){
        try{
            $conn = new mysqli($this->host,$this->db_user,$this->db_password,$this->db_name);
            return $conn;
        }catch(MySQLiException $e){
            echo "Error: ".$e->getMessage()."<br>";
            die();
        }
    }
}