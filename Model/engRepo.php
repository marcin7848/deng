<?php

require_once './db_connect.php';
require_once './Entity/eng.php';
require_once './Model/configRepo.php';
require_once './Model/worksRepo.php';

class engRepo
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

    public function getEng(){
        $query = "SELECT * FROM eng;";
        $query = $this->db->getQuery($query);
        $listOfEng = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $eng = new eng();
            $eng->setEng($result['id'], $result['polish'], $result['english'], $result['repeated'],
                $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect'], $result['thisUnit']);
            $listOfEng[] = $eng;
        }

        return $listOfEng;
    }

    public function getNoteOne($id){
        $query = "SELECT * FROM eng WHERE id=".$id.";";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $eng = new eng();
        $eng->setEng($result['id'], $result['polish'], $result['english'], $result['repeated'],
            $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect'], $result['thisUnit']);

        return $eng;
    }


    public function changeThisUnit($id){
        $query = "UPDATE eng SET thisUnit = IF(thisUnit = 0, 1, 0) WHERE id='".$id."'";
        $this->db->getQuery($query);
    }

    public function editEnglish($id, $english){
        $query = "UPDATE eng SET english=:newEnglish WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newEnglish', $english);
        $stmt->execute();
    }

    public function editPolish($id, $polish){
        $query = "UPDATE eng SET polish=:newPolish WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newPolish', $polish);
        $stmt->execute();
    }

    public function addNewWord($polish, $english){
        $query = "SELECT COUNT(*) AS count FROM eng WHERE english='".addslashes($english)."' OR polish='".addslashes($polish)."'";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        if($count != 0){
            showError("Takie słowo już istnieje!!!");
            return 0;
        }

        $query = "INSERT INTO eng(polish, english, repeated, done, today, lastPerfectNum, lastPerfect, thisUnit) VALUES(:polish, :english, 0, 0, 0, 0, 0, 0);";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':polish', $polish);
        $stmt->bindParam(':english', $english);
        $stmt->execute();

        $this->worksRepo->updateCountDone(2);

        global $activateFromGirls;
        if($activateFromGirls == 1){
            sendFromGirls("eng" , "", $english, $polish, "", "", 0, 0);
        }

    }

    public function deleteThisUnit(){
        $query = "UPDATE eng SET thisUnit = 0 WHERE thisUnit = 1";
        $this->db->getQuery($query);
    }

    public function resetAllWords(){
        $query = "UPDATE eng SET done = 0;";
        $this->db->getQuery($query);
    }

    public function resetWordsWithRange($from, $to){
        $query = "UPDATE eng SET done = 0 WHERE id BETWEEN '".$from."' AND '".$to."'";
        $this->db->getQuery($query);
    }

    public function getCountToEndAll(){
        $query = "SELECT COUNT(*) AS count FROM eng WHERE done<>3;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        return $count;
    }


    public function countWordsToEnd(){
        $query = "SELECT COUNT(*) AS count FROM eng WHERE today=1 AND done=0;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $countDouble = $result['count'];

        if($this->configRepo->getConfig('modeEng') == 3)
        {
            $countDouble = $countDouble*2;
        }

        $query = "SELECT COUNT(*) AS count FROM eng WHERE today=1 AND (done=1 OR done=2);";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $countSingle = $result['count'];

        return $countDouble+$countSingle;
    }

    public function setRepeatWords($from, $to){
        $query = "UPDATE eng SET today = 1, done = 0  WHERE id BETWEEN '".$from."' AND '".$to."'";
        $this->db->getQuery($query);
        $this->configRepo->setConfig('repeatStartedEng', '1');
    }

    public function breakRepeatWords()
    {
        $query = "UPDATE eng SET today = 0, done = 0  WHERE today = 1 AND (done=1 OR done=2);";
        $this->db->getQuery($query);
        $query = "UPDATE eng SET today = 0 WHERE today = 1;";
        $this->db->getQuery($query);
        $this->configRepo->setConfig('repeatStartedEng', '0');
    }


    public function setTodayWords(){
        $todaysEngWords = $this->configRepo->getConfig('todaysEngWords');

        $thisUnitEng = $this->configRepo->getConfig('thisUnitEng');

        if($thisUnitEng == 1){
            $query = "UPDATE eng SET today = 1, done = 0 WHERE thisUnit = 1;";
            $this->db->getQuery($query);
        }

        $query = "UPDATE eng SET today = 1, done = 0, lastPerfect=0, lastPerfectNum=0 WHERE lastPerfect = 1;";
        $this->db->getQuery($query);

        $query = "UPDATE eng SET today = 1 WHERE today = 0 AND done=0 AND thisUnit = 0 ORDER BY RAND() LIMIT ".$todaysEngWords.";";
        $this->db->getQuery($query);

        $this->configRepo->setConfig('todayStartedEng', '1');
    }


    public function breakTodayWords(){
        $query = "SELECT * FROM eng WHERE today=1 AND lastPerfectNum > 4;";
        $query = $this->db->getQuery($query);

        while($result = $query->fetch(PDO::FETCH_BOTH))
        {
            $query2 = "UPDATE eng SET lastPerfect=1 WHERE id=".$result['id'];
            $this->db->getQuery($query2);
        }

        $query = "UPDATE eng SET done=0 WHERE today = 1 AND done <> 3;";
        $this->db->getQuery($query);

        $query = "UPDATE eng SET today = 0, lastPerfectNum=0 WHERE today = 1;";
        $this->db->getQuery($query);

        $this->configRepo->setConfig('todayStartedEng', '0');

    }

    public function showTodayWord(){
        $query = "SELECT * FROM eng WHERE today=1 AND done <> 3 ORDER BY RAND() LIMIT 1;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);

        $modeEng = $this->configRepo->getConfig('modeEng');

        $id = $result['id'];
        $tab["id"] = $id;

        $wordSide = 0;

        if($modeEng == 1){
            $tab["word"] = $result['polish'];
            $tab["answer"] = $result['english'];
            $wordSide = 1;
        }
        else if($modeEng == 2){
            $tab["word"] = $result['english'];
            $tab["answer"] = $result['polish'];
            $wordSide = 2;
        }
        else{
            if($result['done'] == 1){
                $tab["word"] = $result['english'];
                $tab["answer"] = $result['polish'];
                $wordSide = 2;
            }
            else if($result['done'] == 2){
                $tab["word"] = $result['polish'];
                $tab["answer"] = $result['english'];
                $wordSide = 1;
            }
            else{
                $rand = mt_rand(1,2);
                if($rand == 1)
                {
                    $tab["word"] = $result['polish'];
                    $tab["answer"] = $result['english'];
                    $wordSide = 1;
                }
                else{
                    $tab["word"] = $result['english'];
                    $tab["answer"] = $result['polish'];
                    $wordSide = 2;
                }
            }
        }

        $tab["wordSide"] = $wordSide; //1 -> podane pl // 2-> podane eng

        $workingEng = $this->configRepo->getConfig('workingEng');

        if($workingEng == 1){
            $tab["otherAnswers"] = array();
        }
        else if($workingEng == 2){
            $query = "SELECT * FROM eng WHERE today=1 AND id <> ".$id." ORDER BY RAND() LIMIT 3;";
            $query = $this->db->getQuery($query);
            while($result2 = $query->fetch(PDO::FETCH_BOTH)){
                if($wordSide == 1) {
                    $tab["otherAnswers"][] = $result2['polish'];
                }else{
                    $tab["otherAnswers"][] = $result2['english'];
                }
            }
        }
        else{
            $rand = mt_rand(1,2);
            if($rand == 1){
                $tab["otherAnswers"] = array();
            }
            else{
                $query = "SELECT * FROM eng WHERE today=1 AND id <> ".$id." ORDER BY RAND() LIMIT 3;";
                $query = $this->db->getQuery($query);
                while($result2 = $query->fetch(PDO::FETCH_BOTH)){
                    if($wordSide == 1) {
                        $tab["otherAnswers"][] = $result2['english'];
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
            $query = "SELECT * FROM eng WHERE  id = ".$id." AND english='".$answer."';";
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
            $query = "SELECT * FROM eng WHERE  id = ".$id." AND polish='".$answer."';";
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
        $repeatStartedEng = $this->configRepo->getConfig('repeatStartedEng');
        $todayStartedEng = $this->configRepo->getConfig('todayStartedEng');
        $modeEng = $this->configRepo->getConfig('modeEng');

        $eng = $this->getNoteOne($id);

        if($checkAnswerToday == 1){
            if($modeEng == 1){
                $this->updateDone($id, 3);
                $this->updateRepeatedOneMore($id);
            }else if($modeEng == 2){
                $this->updateDone($id, 3);
                $this->updateRepeatedOneMore($id);
            }
            else{
                if($eng->getDone() == 2){
                    $this->updateDone($id, 3);
                    $this->updateRepeatedOneMore($id);
                }
                else if($eng->getDone() == 1)
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

        if($todayStartedEng == 1){
            if($checkAnswerToday == 0){
                $query = "UPDATE eng SET lastPerfectNum = lastPerfectNum + 1 WHERE id = ".$id.";";
                $this->db->getQuery($query);
            }
        }

        if($this->countWordsToEnd() == 0){
            if($todayStartedEng == 1) {
                $this->breakTodayWords();
            }
            if($repeatStartedEng == 1) {
                $this->breakRepeatWords();
            }
        }


    }

    public function updateDone($id, $done){
        $query = "UPDATE eng SET done = ".$done." WHERE id = ".$id.";";
        $this->db->getQuery($query);
    }

    public function updateRepeatedOneMore($id){
        $query = "UPDATE eng SET repeated = repeated+1 WHERE id = ".$id.";";
        $this->db->getQuery($query);

        $query = "UPDATE note SET eng = eng + 1 WHERE date = CURDATE();";
        $this->db->getQuery($query);

        $this->worksRepo->updateCountDone(1);
    }

}