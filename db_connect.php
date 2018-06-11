<?php

require_once "./config.php";

class db_connect {
    private $pdo;

    public function __construct(){
        try{
          global $host, $user, $pass, $database_name;
          $this->pdo = new PDO('mysql:host='.$host.';dbname='.$database_name.'', $user, $pass);
		  $this->pdo->exec("set names utf8");
        }catch(PDOException $e){
          exit('Połączenie nie mogło zostać utworzone: ' . $e->getMessage());
        }

    }

    public function getQuery($query){
         return $stmt = $this->pdo->query($query);
    }

    public function prepareQuery($query){
        return $stmt = $this->pdo->prepare($query);
    }

}