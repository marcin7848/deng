<?php

require_once './db_connect.php';
require_once './Entity/deu.php';
require_once './Model/configRepo.php';
require_once './Model/worksRepo.php';

class deuRepo
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

    public function getDeu(){
        $query = "SELECT * FROM deu;";
        $query = $this->db->getQuery($query);
        $listOfDeu = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $deu = new deu();
            $deu->setDeu($result['id'], $result['polish'], $result['deutsch'], $result['repeated'],
                $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect'], $result['thisUnit']);
            $listOfDeu[] = $deu;
        }

        return $listOfDeu;
    }

    public function getNoteOne($id){
        $query = "SELECT * FROM deu WHERE id=".$id.";";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $deu = new deu();
        $deu->setDeu($result['id'], $result['polish'], $result['deutsch'], $result['repeated'],
            $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect'], $result['thisUnit']);

        return $deu;
    }


    public function changeThisUnit($id){
        $query = "UPDATE deu SET thisUnit = IF(thisUnit = 0, 1, 0) WHERE id='".$id."'";
        $this->db->getQuery($query);
    }

    public function editDeutsch($id, $deutsch){
        $query = "UPDATE deu SET deutsch=:newDeutsch WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newDetusch', $deutsch);
        $stmt->execute();
    }

    public function editPolish($id, $polish){
        $query = "UPDATE deu SET polish=:newPolish WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newPolish', $polish);
        $stmt->execute();
    }

    public function addNewWord($polish, $deutsch){
        $query = "SELECT COUNT(*) AS count FROM deu WHERE deutsch='".addslashes($deutsch)."' OR polish='".addslashes($polish)."'";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        if($count != 0){
            showError("Takie słowo już istnieje!!!");
            return 0;
        }

        $query = "INSERT INTO deu(polish, deutsch, repeated, done, today, lastPerfectNum, lastPerfect, thisUnit) VALUES(:polish, :deutsch, 0, 0, 0, 0, 0, 0);";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':polish', $polish);
        $stmt->bindParam(':deutsch', $deutsch);
        $stmt->execute();
        $this->worksRepo->updateCountDone(6);

        global $activateFromGirls;
        if($activateFromGirls == 1){
            sendFromGirls("deu" , "", $deutsch, $polish, "", "", 0, 0);
        }

    }

    public function deleteThisUnit(){
        $query = "UPDATE deu SET thisUnit = 0 WHERE thisUnit = 1";
        $this->db->getQuery($query);
    }

    public function resetAllWords(){
        $query = "UPDATE deu SET done = 0;";
        $this->db->getQuery($query);
    }

    public function resetWordsWithRange($from, $to){
        $query = "UPDATE deu SET done = 0 WHERE id BETWEEN '".$from."' AND '".$to."'";
        $this->db->getQuery($query);
    }

    public function getCountToEndAll(){
        $query = "SELECT COUNT(*) AS count FROM deu WHERE done<>3;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        return $count;
    }


    public function countWordsToEnd(){
        $query = "SELECT COUNT(*) AS count FROM deu WHERE today=1 AND done=0;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $countDouble = $result['count'];

        if($this->configRepo->getConfig('modeDeu') == 3)
        {
            $countDouble = $countDouble*2;
        }

        $query = "SELECT COUNT(*) AS count FROM deu WHERE today=1 AND (done=1 OR done=2);";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $countSingle = $result['count'];

        return $countDouble+$countSingle;
    }

    public function setRepeatWords($from, $to){
        $query = "UPDATE deu SET today = 1, done = 0  WHERE id BETWEEN '".$from."' AND '".$to."'";
        $this->db->getQuery($query);
        $this->configRepo->setConfig('repeatStartedDeu', '1');
    }

    public function breakRepeatWords()
    {
        $query = "UPDATE deu SET today = 0, done = 0  WHERE today = 1 AND (done=1 OR done=2);";
        $this->db->getQuery($query);
        $query = "UPDATE deu SET today = 0 WHERE today = 1;";
        $this->db->getQuery($query);
        $this->configRepo->setConfig('repeatStartedDeu', '0');
    }


    public function setTodayWords(){
        $todaysDeuWords = $this->configRepo->getConfig('todaysDeuWords');

        $thisUnitDeu = $this->configRepo->getConfig('thisUnitDeu');

        if($thisUnitDeu == 1){
            $query = "UPDATE deu SET today = 1, done = 0 WHERE thisUnit = 1;";
            $this->db->getQuery($query);
        }

        $query = "UPDATE deu SET today = 1, done = 0, lastPerfect=0, lastPerfectNum=0 WHERE lastPerfect = 1;";
        $this->db->getQuery($query);

        $query = "UPDATE deu SET today = 1 WHERE today = 0 AND done=0 AND thisUnit = 0 ORDER BY RAND() LIMIT ".$todaysDeuWords.";";
        $this->db->getQuery($query);

        $this->configRepo->setConfig('todayStartedDeu', '1');
    }


    public function breakTodayWords(){
        $query = "SELECT * FROM deu WHERE today=1 AND lastPerfectNum > 4;";
        $query = $this->db->getQuery($query);

        while($result = $query->fetch(PDO::FETCH_BOTH))
        {
            $query2 = "UPDATE deu SET lastPerfect=1 WHERE id=".$result['id'];
            $this->db->getQuery($query2);
        }

        $query = "UPDATE deu SET done=0 WHERE today = 1 AND done <> 3;";
        $this->db->getQuery($query);

        $query = "UPDATE deu SET today = 0, lastPerfectNum=0 WHERE today = 1;";
        $this->db->getQuery($query);

        $this->configRepo->setConfig('todayStartedDeu', '0');

    }

    public function showTodayWord(){
        $query = "SELECT * FROM deu WHERE today=1 AND done <> 3 ORDER BY RAND() LIMIT 1;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);

        $modeDeu = $this->configRepo->getConfig('modeDeu');

        $id = $result['id'];
        $tab["id"] = $id;

        $wordSide = 0;

        if($modeDeu == 1){
            $tab["word"] = $result['polish'];
            $tab["answer"] = $result['deutsch'];
            $wordSide = 1;
        }
        else if($modeDeu == 2){
            $tab["word"] = $result['deutsch'];
            $tab["answer"] = $result['polish'];
            $wordSide = 2;
        }
        else{
            if($result['done'] == 1){
                $tab["word"] = $result['deutsch'];
                $tab["answer"] = $result['polish'];
                $wordSide = 2;
            }
            else if($result['done'] == 2){
                $tab["word"] = $result['polish'];
                $tab["answer"] = $result['deutsch'];
                $wordSide = 1;
            }
            else{
                $rand = mt_rand(1,2);
                if($rand == 1)
                {
                    $tab["word"] = $result['polish'];
                    $tab["answer"] = $result['deutsch'];
                    $wordSide = 1;
                }
                else{
                    $tab["word"] = $result['deutsch'];
                    $tab["answer"] = $result['polish'];
                    $wordSide = 2;
                }
            }
        }

        $tab["wordSide"] = $wordSide; //1 -> podane pl // 2-> podane deu

        $workingDeu = $this->configRepo->getConfig('workingDeu');

        if($workingDeu == 1){
            $tab["otherAnswers"] = array();
        }
        else if($workingDeu == 2){
            $query = "SELECT * FROM deu WHERE today=1 AND id <> ".$id." ORDER BY RAND() LIMIT 3;";
            $query = $this->db->getQuery($query);
            while($result2 = $query->fetch(PDO::FETCH_BOTH)){
                if($wordSide == 1) {
                    $tab["otherAnswers"][] = $result2['polish'];
                }else{
                    $tab["otherAnswers"][] = $result2['deutsch'];
                }
            }
        }
        else{
            $rand = mt_rand(1,2);
            if($rand == 1){
                $tab["otherAnswers"] = array();
            }
            else{
                $query = "SELECT * FROM deu WHERE today=1 AND id <> ".$id." ORDER BY RAND() LIMIT 3;";
                $query = $this->db->getQuery($query);
                while($result2 = $query->fetch(PDO::FETCH_BOTH)){
                    if($wordSide == 1) {
                        $tab["otherAnswers"][] = $result2['deutsch'];
                    }else{
                        $tab["otherAnswers"][] = $result2['polish'];
                    }
                }
            }
        }

        return $tab;
    }

    public function checkAnswerToday($id, $wordSide, $answer){
        if($wordSide == 1){
            $query = "SELECT * FROM deu WHERE  id = ".$id." AND deutsch='".$answer."';";
            $query = $this->db->getQuery($query);
            $result = $query->fetch(PDO::FETCH_BOTH);
            if($result){
                return 1;
            }
            else{
                return 0;
            }
        }
        else{
            $query = "SELECT * FROM deu WHERE  id = ".$id." AND polish='".$answer."';";
            $query = $this->db->getQuery($query);
            $result = $query->fetch(PDO::FETCH_BOTH);
            if($result){
                return 1;
            }
            else{
                return 0;
            }
        }

    }

    public function saveAnswerToday($id, $checkAnswerToday, $wordSide){
        $repeatStartedDeu = $this->configRepo->getConfig('repeatStartedDeu');
        $todayStartedDeu = $this->configRepo->getConfig('todayStartedDeu');
        $modeDeu = $this->configRepo->getConfig('modeDeu');

        $deu = $this->getNoteOne($id);

        if($checkAnswerToday == 1){
            if($modeDeu == 1){
                $this->updateDone($id, 3);
                $this->updateRepeatedOneMore($id);
            }else if($modeDeu == 2){
                $this->updateDone($id, 3);
                $this->updateRepeatedOneMore($id);
            }
            else{
                if($deu->getDone() == 2){
                    $this->updateDone($id, 3);
                    $this->updateRepeatedOneMore($id);
                }
                else if($deu->getDone() == 1)
                {
                    $this->updateDone($id, 3);
                    $this->updateRepeatedOneMore($id);
                }
                else{
                    if($wordSide == 2){
                        $this->updateDone($id, 2);
                    }
                    else{
                        $this->updateDone($id, 1);
                    }
                }
            }
        }

        if($todayStartedDeu == 1){
            if($checkAnswerToday == 0){
                $query = "UPDATE deu SET lastPerfectNum = lastPerfectNum + 1 WHERE id = ".$id.";";
                $this->db->getQuery($query);
            }
        }

        if($this->countWordsToEnd() == 0){
            if($todayStartedDeu == 1) {
                $this->breakTodayWords();
            }
            if($repeatStartedDeu == 1) {
                $this->breakRepeatWords();
            }
        }


    }

    public function updateDone($id, $done){
        $query = "UPDATE deu SET done = ".$done." WHERE id = ".$id.";";
        $this->db->getQuery($query);
    }

    public function updateRepeatedOneMore($id){
        $query = "UPDATE deu SET repeated = repeated+1 WHERE id = ".$id.";";
        $this->db->getQuery($query);

        $query = "UPDATE note SET deu = deu + 1 WHERE date = CURDATE();";
        $this->db->getQuery($query);

        $this->worksRepo->updateCountDone(5);
    }

}