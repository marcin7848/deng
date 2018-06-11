<?php

require_once './db_connect.php';
require_once './Entity/exeEngName.php';
require_once './Model/configRepo.php';
require_once './Model/worksRepo.php';

class exeEngNameRepo
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


    public function getExeEngName(){
        $query = "SELECT * FROM exeengname;";
        $query = $this->db->getQuery($query);
        $listOfExeEngName = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $exeEngName = new exeEngName();
            $exeEngName->setExeEngName($result['id'], $result['name']);
            $listOfExeEngName[] = $exeEngName;
        }

        return $listOfExeEngName;
    }

    public function editName($id, $name){
        $query = "UPDATE exeengname SET name=:newName WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newName', $name);
        $stmt->execute();
    }

    public function addNewExeName($name){
        $query = "SELECT COUNT(*) AS count FROM exeengname WHERE name='".addslashes($name)."'";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        if($count != 0){
            showError("Takie ćwiczenie już istnieje!!!");
            return 0;
        }

        $query = "INSERT INTO exeengname(name) VALUES(:newName);";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newName', $name);
        $stmt->execute();

        global $activateFromGirls;
        if($activateFromGirls == 1){
            sendFromGirls("exeengname" ,$name, "", "", "", "", 0, 0);
        }
    }


}