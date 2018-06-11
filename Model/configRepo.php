<?php

require_once './db_connect.php';
require_once './Entity/config.php';

class configRepo
{
    private $db;

    public function __construct()
    {
        $this->db = new db_connect();
    }

    public function getConfig($name){
        $query = "SELECT value FROM config WHERE name='".$name."';";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        return $result['value'];
    }


    public function setConfig($name, $value){
        $query = "UPDATE config SET value=:newValue WHERE name='".$name."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newValue', $value);
        $stmt->execute();
    }
}