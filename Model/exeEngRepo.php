<?php

require_once './db_connect.php';
require_once './Entity/exeEng.php';
require_once './Model/configRepo.php';
require_once './Model/worksRepo.php';
require_once './Model/exeEngNameRepo.php';

class exeEngRepo
{
    private $db;
    private $configRepo;
    private $worksRepo;
    private $exeEngNameRepo;

    public function __construct()
    {
        $this->db = new db_connect();
        $this->configRepo = new configRepo();
        $this->worksRepo = new worksRepo();
        $this->exeEngNameRepo = new exeEngNameRepo();
    }


    public function getExeEng(){
        $query = "SELECT * FROM exeeng;";
        $query = $this->db->getQuery($query);
        $listOfExeEng = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $exeEng = new exeEng();
            $exeEng->setExeEng($result['id'], $result['idExeName'], $result['first'], $result['second'],
                $result['word'], $result['comment'], $result['working'], $result['mode'], $result['repeated'],
                $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect']);
            $listOfExeEng[] = $exeEng;
        }

        return $listOfExeEng;
    }

    public function getExeEngById($idExeName){
        $query = "SELECT * FROM exeeng WHERE idExeName='".$idExeName."';";
        $query = $this->db->getQuery($query);
        $listOfExeEng = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $exeEng = new exeEng();
            $exeEng->setExeEng($result['id'], $result['idExeName'], $result['first'], $result['second'],
                $result['word'], $result['comment'], $result['working'], $result['mode'], $result['repeated'],
                $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect']);
            $listOfExeEng[] = $exeEng;
        }

        return $listOfExeEng;
    }

    public function getExeOne($id){
        $query = "SELECT * FROM exeeng WHERE id=".$id.";";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $exeEng = new exeEng();
        $exeEng->setExeEng($result['id'], $result['idExeName'], $result['first'], $result['second'],
            $result['word'], $result['comment'], $result['working'], $result['mode'], $result['repeated'],
            $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect']);

        return $exeEng;
    }

    public function getExeEngJustToday(){
        $query = "SELECT * FROM exeeng WHERE today=1;";
        $query = $this->db->getQuery($query);
        $listOfExeEng = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $exeEng = new exeEng();
            $exeEng->setExeEng($result['id'], $result['idExeName'], $result['first'], $result['second'],
                $result['word'], $result['comment'], $result['working'], $result['mode'], $result['repeated'],
                $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect']);
            $listOfExeEng[] = $exeEng;
        }

        return $listOfExeEng;
    }

    public function getExeEngSort(){
        $exeEngNames = $this->exeEngNameRepo->getExeEngName();

        $listOfFull = array();

        foreach($exeEngNames as $eEN){
            $exeEngNameId = $eEN->getId();
            $listOfExeEng = $this->getExeEngById($exeEngNameId);
            foreach($listOfExeEng as $sE){
                $listOfFull[] = $sE;
            }
        }

        return $listOfFull;
    }

    public function prepareToShowExeEngSort($showExeEng){
        $exeEngName = $this->exeEngNameRepo->getExeEngName();

        foreach($showExeEng as $sEE){
            foreach($exeEngName as $sEN){
                if($sEE->getIdExeName() == $sEN->getId()){
                    $sEE->setIdExeName($sEN->getName());
                    break;
                }
            }
        }

        return $showExeEng;
    }

    public function countWordsToEnd(){
        $exeEng = $this->getExeEngJustToday();

        $allToEnd = 0;
        foreach($exeEng as $eE){
            if($eE->getDone() != 3){
                if($eE->getMode() == 2){
                    $allToEnd++;
                }
                else{
                    $allToEnd++;
                    if(($eE->getDone() == 0) && ($eE->getWorking() == 3)){
                        $allToEnd++;
                    }
                }

            }
        }

        return $allToEnd;
    }

    public function getCountToEndAll(){
        $query = "SELECT COUNT(*) AS count FROM exeeng WHERE done<>3;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        return $count;
    }

    public function addNewExe($idExeName, $first, $second, $word, $comment, $working, $mode){

        if($mode == 2){
            $working = 1;
            if(empty($word)){
                showError("Pole WORD nie może być puste, bo wybrałeś mode na wybór opcji/wpisanie w odpowiedniej formie. ");
                return 0;
            }
            if(strpos($first, '...') === FALSE){
                showError("Postaw '...' w first przy trybie wyboru z listy!");
                return 0;
            }
        }
        if(empty($word)){
            $word = "-";
        }

        if(empty($comment)){
            $comment = "-";
        }

        $query = "SELECT COUNT(*) AS count FROM exeeng WHERE first='".addslashes($first)."'";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        if($count != 0){
            showError("Takie ćwiczenie już prawdopodobnie istnieje!");
            return 0;
        }

        $query = "INSERT INTO exeeng(idExeName, first, second, word, comment, working, mode, repeated, done, today, lastPerfectNum, lastPerfect) VALUES(:idExeName, :first, :second, :word, :comment, :working, :mode, 0,0,0,0,0);";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':idExeName', $idExeName);
        $stmt->bindParam(':first', $first);
        $stmt->bindParam(':second', $second);
        $stmt->bindParam(':word', $word);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':working', $working);
        $stmt->bindParam(':mode', $mode);
        $stmt->execute();

        $this->worksRepo->updateCountDone(4);

        global $activateFromGirls;
        if($activateFromGirls == 1){

            $query = "SELECT * FROM exeengname WHERE id=".$idExeName;
            $query = $this->db->getQuery($query);
            $result = $query->fetch(PDO::FETCH_BOTH);
            $exeName = $result['name'];

            sendFromGirls("exeeng" , $exeName, $first, $second, $word, $comment, $working, $mode);
        }

    }

    public function editFirst($id, $first){
        $query = "UPDATE exeeng SET first=:newFirst WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newFirst', $first);
        $stmt->execute();
    }

    public function editSecond($id, $second){
        $query = "UPDATE exeeng SET second=:newSecond WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newSecond', $second);
        $stmt->execute();
    }

    public function editWord($id, $word){
        $query = "UPDATE exeeng SET word=:newWord WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newWord', $word);
        $stmt->execute();
    }

    public function editComment($id, $comment){
        $query = "UPDATE exeeng SET comment=:newComment WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newComment', $comment);
        $stmt->execute();
    }

    public function editWorking($id, $working){
        $query = "UPDATE exeeng SET working=:newWorking WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newWorking', $working);
        $stmt->execute();
    }

    public function editMode($id, $mode){
        $query = "UPDATE exeeng SET mode=:newMode WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newMode', $mode);
        $stmt->execute();
    }

    public function editExeName($id, $idExeName){
        $query = "UPDATE exeeng SET idExeName=:newIdExeName WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newIdExeName', $idExeName);
        $stmt->execute();
    }

    public function breakRepeatWords()
    {
        $query = "UPDATE exeeng SET today = 0, done = 0  WHERE today = 1 AND (done=1 OR done=2);";
        $this->db->getQuery($query);
        $query = "UPDATE exeeng SET today = 0 WHERE today = 1;";
        $this->db->getQuery($query);
        $this->configRepo->setConfig('repeatStartedExeEng', '0');
    }

    public function resetAllWords(){
        $query = "UPDATE exeeng SET done = 0;";
        $this->db->getQuery($query);
    }

    public function resetWordsWithRange($from, $to){
        if(!empty($to)) {
            $query = "UPDATE exeeng SET done = 0 WHERE idExeName BETWEEN '" . $from . "' AND '" . $to . "'";
            $this->db->getQuery($query);
            showMessage("Zresetowano ćwiczenia z zakresu: ".$from."- ".$to);
        }
        else{
            $idExeNames = $this->selectIdFromRange($from);
            foreach($idExeNames as $eN) {
                $query = "UPDATE exeeng SET done = 0 WHERE idExeName BETWEEN '" . $eN . "' AND '" . $eN . "'";
                $this->db->getQuery($query);
            }
            showMessage("Zresetowano ćwiczenia o ID: ".$from);
        }
    }

    public function selectIdFromRange($range){
        if(strpos($range, ',')){
            $exp = explode(',', $range);
            $tab = array();
            foreach($exp as $e){
                $tab[] = $e;
            }
            return $tab;
        }else{
            return array($range);
        }
    }

    public function breakTodayWords(){
        $query = "SELECT * FROM exeeng WHERE today=1 AND lastPerfectNum > 4;";
        $query = $this->db->getQuery($query);

        while($result = $query->fetch(PDO::FETCH_BOTH))
        {
            $query2 = "UPDATE exeeng SET lastPerfect=1 WHERE id=".$result['id'];
            $this->db->getQuery($query2);
        }

        $query = "UPDATE exeeng SET done=0 WHERE today = 1 AND done <> 3;";
        $this->db->getQuery($query);

        $query = "UPDATE exeeng SET today = 0, lastPerfectNum=0 WHERE today = 1;";
        $this->db->getQuery($query);

        $this->configRepo->setConfig('todayStartedExeEng', '0');

    }

    public function setRepeatWords($from, $to, $countEachExe){
        if(!empty($to)) {
            $query = "SELECT * FROM exeeng WHERE idExeName BETWEEN '".$from."' AND '".$to."';";
            $query = $this->db->getQuery($query);
            while($result = $query->fetch(PDO::FETCH_BOTH))
            {
                $query2 = "UPDATE exeeng SET today=1 WHERE done = 0 AND idExeName=".$result['idExeName']." ORDER BY RAND() LIMIT ".$countEachExe.";";
                $this->db->getQuery($query2);
            }
        }else{
            $idExeNames = $this->selectIdFromRange($from);
            foreach($idExeNames as $eN) {
                $query = "UPDATE exeeng SET today=1 WHERE done = 0 AND idExeName BETWEEN '" . $eN . "' AND '" . $eN . "' ORDER BY RAND() LIMIT ".$countEachExe.";";
                $this->db->getQuery($query);
            }
        }

        $this->configRepo->setConfig('repeatStartedExeEng', '1');
    }


    public function setTodayWords($countAllWords){

        $query = "UPDATE exeeng SET today = 1, done = 0, lastPerfect=0, lastPerfectNum=0 WHERE lastPerfect = 1;";
        $this->db->getQuery($query);

        $query = "UPDATE exeeng SET today = 1 WHERE today = 0 AND done=0 ORDER BY RAND() LIMIT ".$countAllWords.";";
        $this->db->getQuery($query);

        $this->configRepo->setConfig('todayStartedExeEng', '1');
    }

    public function showTodayWord(){
        $query = "SELECT * FROM exeeng WHERE today=1 AND done <> 3 ORDER BY RAND() LIMIT 1;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);

        $mode = $result['mode'];
        $working = $result['working'];
        $tab["working"] = $working;

        $id = $result['id'];

        $tab["id"] = $id;
        $tab["comment"] = $result['comment'];
        $tab["word"] = $result['word'];
        $first = $result['first'];
        $second = $result['second'];
        $word = $result['word'];

        $wordSide = 0;


        if($working == 1){
            $tab["question"] = $result['first'];
            $tab["answer"] = $result['second'];
            $wordSide = 1;
        }
        else if($working == 2){
            $tab["question"] = $result['second'];
            $tab["answer"] = $result['first'];
            $wordSide = 2;
        }
        else{
            if($result['done'] == 1){
                $tab["question"] = $result['second'];
                $tab["answer"] = $result['first'];
                $wordSide = 2;
            }
            else if($result['done'] == 2){
                $tab["question"] = $result['first'];
                $tab["answer"] = $result['second'];
                $wordSide = 1;
            }
            else{
                $rand = mt_rand(1,2);
                if($rand == 1)
                {
                    $tab["question"] = $result['first'];
                    $tab["answer"] = $result['second'];
                    $wordSide = 1;
                }
                else{
                    $tab["question"] = $result['second'];
                    $tab["answer"] = $result['first'];
                    $wordSide = 2;
                }
            }
        }

        $tab["wordSide"] = $wordSide; //1 -> podane first // 2-> podane second


        if($mode == 1){
            $tab["otherAnswers"] = array();
        }
        else{
            $sides = explode('...', $first);

            $rand = mt_rand(1,2);
            if($rand == 1){
                if(empty($sides[0])){
                    $sides[0] = "";
                }
                if(empty($sides[1])){
                    $sides[1] = "";
                }
                $tab["question"] = $sides[0].' ... ('.$word.') '.$sides[1];
                $tab["otherAnswers"] = array();
            }
            else{
                $query = "SELECT * FROM exeeng WHERE id <> ".$id." AND word='".$word."' AND second <> '".$second."' ORDER BY RAND() LIMIT 3;";
                $query = $this->db->getQuery($query);
                $otherAnswers = array();
                while($result = $query->fetch(PDO::FETCH_BOTH)){
                    $otherAnswers[] = $result['second'];
                }

                if(count($otherAnswers) >= 3){
                    shuffle($otherAnswers);
                    shuffle($otherAnswers);
                    shuffle($otherAnswers);
                    $tab["otherAnswers"] = array($otherAnswers[0], $otherAnswers[1], $otherAnswers[2]);
                    if(empty($sides[0])){
                        $sides[0] = "";
                    }
                    if(empty($sides[1])){
                        $sides[1] = "";
                    }
                    $tab["question"] = $sides[0].' ... '.$sides[1];
                }
                else{
                    if(empty($sides[0])){
                        $sides[0] = "";
                    }
                    if(empty($sides[1])){
                        $sides[1] = "";
                    }
                    $tab["question"] = $sides[0].' ... ('.$word.') '.$sides[1];
                    $tab["otherAnswers"] = array();
                }
            }
        }

        return $tab;
    }

    public function checkAnswerToday($id, $wordSide, $answer){
        if($wordSide == 1){
            $query = "SELECT * FROM exeeng WHERE  id = ".$id." AND second='".$answer."';";
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
            $query = "SELECT * FROM exeeng WHERE  id = ".$id." AND first='".$answer."';";
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

    public function saveAnswerToday($id, $checkAnswerToday, $wordSide, $working){
        $repeatStarted = $this->configRepo->getConfig('repeatStartedExeEng');
        $todayStarted = $this->configRepo->getConfig('todayStartedExeEng');

        $exe = $this->getExeOne($id);

        if($checkAnswerToday == 1){
            if($working == 1){
                $this->updateDone($id, 3);
                $this->updateRepeatedOneMore($id);
            }else if($working == 2){
                $this->updateDone($id, 3);
                $this->updateRepeatedOneMore($id);
            }
            else{
                if($exe->getDone() == 2){
                    $this->updateDone($id, 3);
                    $this->updateRepeatedOneMore($id);
                }
                else if($exe->getDone() == 1)
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

        if($todayStarted == 1){
            if($checkAnswerToday == 0){
                $query = "UPDATE exeeng SET lastPerfectNum = lastPerfectNum + 1 WHERE id = ".$id.";";
                $this->db->getQuery($query);
            }
        }

        if($this->countWordsToEnd() == 0){
            if($todayStarted == 1) {
                $this->breakTodayWords();
            }
            if($repeatStarted == 1) {
                $this->breakRepeatWords();
            }
        }
    }

    public function updateDone($id, $done){
        $query = "UPDATE exeeng SET done = ".$done." WHERE id = ".$id.";";
        $this->db->getQuery($query);
    }

    public function updateRepeatedOneMore($id){
        $query = "UPDATE exeeng SET repeated = repeated+1 WHERE id = ".$id.";";
        $this->db->getQuery($query);

        $query = "UPDATE note SET engExe = engExe + 1 WHERE date = CURDATE();";
        $this->db->getQuery($query);

        $this->worksRepo->updateCountDone(3);
    }

}