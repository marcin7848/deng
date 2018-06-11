<?php

require_once './db_connect.php';
require_once './Entity/irVerbsEng.php';
require_once './Model/configRepo.php';
require_once './Model/worksRepo.php';

class irVerbsEngRepo
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

    public function getIrVerbsEng()
    {
        $query = "SELECT * FROM ir_verbs_eng;";
        $query = $this->db->getQuery($query);
        $listOfIrVerbsEng = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $irVerbsEng = new irVerbsEng();
            $irVerbsEng->setIrVerbsEng($result['id'], $result['pl'], $result['inf'], $result['past'],
                $result['pastPart'], $result['done'], $result['repeated']);
            $listOfIrVerbsEng[] = $irVerbsEng;
        }

        return $listOfIrVerbsEng;
    }

    public function getIrVerbsEngOne($id)
    {
        $query = "SELECT * FROM ir_verbs_eng WHERE id=" . $id . ";";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $irVerbsEng = new irVerbsEng();
        $irVerbsEng->setIrVerbsEng($result['id'], $result['pl'], $result['inf'], $result['past'],
            $result['pastPart'], $result['done'], $result['repeated']);

        return $irVerbsEng;
    }

    public function getCountToEnd()
    {
        $query = "SELECT COUNT(*) AS count FROM ir_verbs_eng WHERE done<>3;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        return $count;
    }

    public function addNewIrVerb($pl, $inf, $past, $pastPart)
    {

        $query = "SELECT COUNT(*) AS count FROM ir_verbs_eng WHERE pl='" . addslashes($pl) . "' OR inf='" . addslashes($inf) . "' OR past='" . addslashes($past) . "' OR pastPart='" . addslashes($pastPart) . "'";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        if ($count != 0) {
            showError("Już istnieje taki czasownik!!!");
            return 0;
        }

        $query = "INSERT INTO ir_verbs_eng(pl, inf, past, pastPart, done, repeated) VALUES(:pl, :inf, :past, :pastPart, 0,0);";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':pl', $pl);
        $stmt->bindParam(':inf', $inf);
        $stmt->bindParam(':past', $past);
        $stmt->bindParam(':pastPart', $pastPart);
        $stmt->execute();
    }

    public function resetAllVerbs()
    {
        $query = "UPDATE ir_verbs_eng SET done = 0;";
        $this->db->getQuery($query);
    }

    public function showTodayVerb()
    {
        $query = "SELECT * FROM ir_verbs_eng WHERE done <> 3 ORDER BY RAND() LIMIT 1;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);

        $tab['id'] = $result['id'];
        $tab['pl'] = $result['pl'];
        $tab['inf'] = $result['inf'];
        $tab['past'] = $result['past'];
        $tab['pastPart'] = $result['pastPart'];

        return $tab;
    }

    public function checkAnswerToday($id, $inf, $past, $pastPart)
    {
        $irVerbEng = $this->getIrVerbsEngOne($id);

        $tab = array();

        if($irVerbEng->getInf() == $inf){
            $tab['infChecked'] = 1;
        }
        else{
            $tab['infChecked'] = 0;
        }

        if($irVerbEng->getPast() == $past){
            $tab['pastChecked'] = 1;
        }
        else{
            $tab['pastChecked'] = 0;
        }


        if($irVerbEng->getPastPart() == $pastPart){
            $tab['pastPartChecked'] = 1;
        }
        else{
            $tab['pastPartChecked'] = 0;
        }

        return $tab;
    }

    public function saveAnswerToday($id, $answers){
        if(($answers['infChecked'] == 1) AND ($answers['pastChecked'] == 1) AND ($answers['pastPartChecked'] == 1)){
            $this->updateDone($id, 3);
            $this->updateRepeatedOneMore($id);
        }
    }

    public function updateDone($id, $done){
        $query = "UPDATE ir_verbs_eng SET done = ".$done." WHERE id = ".$id.";";
        $this->db->getQuery($query);
    }

    public function updateRepeatedOneMore($id){
        $query = "UPDATE ir_verbs_eng SET repeated = repeated+1 WHERE id = ".$id.";";
        $this->db->getQuery($query);

        if($this->getCountToEnd() == 0){
            $this->worksRepo->updateCountDone(11);
        }
    }
}