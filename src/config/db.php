<?php

class Db {
    private $dbhost = "127.0.0.1:3306";
    private $dbuser = "dami";
    private $dbpass = "Deneme123!";
    private $dbname = "api_deneme";

    public function connect(){
        $mysql_connection = "mysql:host=$this->dbhost; dbname=$this->dbname; charset=utf8";
        $connection = new PDO($mysql_connection, $this->dbuser, $this->dbpass);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTIONE);
        return $connection;
    }
}
