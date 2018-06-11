<?php

require_once './db_connect.php';
require_once './Entity/exeDeuName.php';
require_once './Model/configRepo.php';
require_once './Model/worksRepo.php';

class exeDeuNameRepo
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


    public function getExeDeuName(){
        $query = "SELECT * FROM exedeuname;";
        $query = $this->db->getQuery($query);
        $listOfExeDeuName = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $exeDeuName = new exeDeuName();
            $exeDeuName->setExeDeuName($result['id'], $result['name']);
            $listOfExeDeuName[] = $exeDeuName;
        }

        return $listOfExeDeuName;
    }

    public function editName($id, $name){
        $query = "UPDATE exedeuname SET name=:newName WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newName', $name);
        $stmt->execute();
    }

    public function addNewExeName($name){
        $query = "SELECT COUNT(*) AS count FROM exedeuname WHERE name='".addslashes($name)."'";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        if($count != 0){
            showError("Takie ćwiczenie już istnieje!!!");
            return 0;
        }

        $query = "INSERT INTO exedeuname(name) VALUES(:newName);";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newName', $name);
        $stmt->execute();

        global $activateFromGirls;
        if($activateFromGirls == 1){
            sendFromGirls("exedeuname" ,$name, "", "", "", "", 0, 0);
        }
    }


}