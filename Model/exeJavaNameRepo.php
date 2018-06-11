<?php

require_once './db_connect.php';
require_once './Entity/exeJavaName.php';
require_once './Model/configRepo.php';
require_once './Model/worksRepo.php';

class exeJavaNameRepo
{
    private $db;
    private $configRepo;
    private $worksRepo;

    public function __construct()
    {
        $this->db = new db_connect();
        $this->configRepo = new configRepo();
        $this->worksRepo = new worksRepo();
    }


    public function getExeJavaName(){
        $query = "SELECT * FROM exejavaname;";
        $query = $this->db->getQuery($query);
        $listOfExeJavaName = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $exeJavaName = new exeJavaName();
            $exeJavaName->setExeJavaName($result['id'], $result['name']);
            $listOfExeJavaName[] = $exeJavaName;
        }

        return $listOfExeJavaName;
    }

    public function editName($id, $name){
        $query = "UPDATE exejavaname SET name=:newName WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newName', $name);
        $stmt->execute();
    }

    public function addNewExeName($name){
        $query = "SELECT COUNT(*) AS count FROM exejavaname WHERE name='".addslashes($name)."'";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        if($count != 0){
            showError("Takie ćwiczenie już istnieje!!!");
            return 0;
        }

        $query = "INSERT INTO exejavaname(name) VALUES(:newName);";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newName', $name);
        $stmt->execute();

    }


}