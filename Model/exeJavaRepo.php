<?php

require_once './db_connect.php';
require_once './Entity/exeJava.php';
require_once './Model/configRepo.php';
require_once './Model/worksRepo.php';
require_once './Model/exeJavaNameRepo.php';

class exeJavaRepo
{
    private $db;
    private $configRepo;
    private $worksRepo;
    private $exeJavaNameRepo;

    public function __construct()
    {
        $this->db = new db_connect();
        $this->configRepo = new configRepo();
        $this->worksRepo = new worksRepo();
        $this->exeJavaNameRepo = new exeJavaNameRepo();
    }


    public function getExeJava(){
        $query = "SELECT * FROM exejava;";
        $query = $this->db->getQuery($query);
        $listOfExeJava = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $exeJava = new exeJava();
            $exeJava->setExeJava($result['id'], $result['idExeName'], $result['first'], $result['second'],
                $result['word'], $result['comment'], $result['working'], $result['mode'], $result['repeated'],
                $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect']);
            $listOfExeJava[] = $exeJava;
        }

        return $listOfExeJava;
    }

    public function getExeJavaById($idExeName){
        $query = "SELECT * FROM exejava WHERE idExeName='".$idExeName."';";
        $query = $this->db->getQuery($query);
        $listOfExeJava = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $exeJava = new exeJava();
            $exeJava->setExeJava($result['id'], $result['idExeName'], $result['first'], $result['second'],
                $result['word'], $result['comment'], $result['working'], $result['mode'], $result['repeated'],
                $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect']);
            $listOfExeJava[] = $exeJava;
        }

        return $listOfExeJava;
    }

    public function getExeOne($id){
        $query = "SELECT * FROM exejava WHERE id=".$id.";";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $exeJava = new exeJava();
        $exeJava->setExeJava($result['id'], $result['idExeName'], $result['first'], $result['second'],
            $result['word'], $result['comment'], $result['working'], $result['mode'], $result['repeated'],
            $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect']);

        return $exeJava;
    }

    public function getExeJavaJustToday(){
        $query = "SELECT * FROM exejava WHERE today=1;";
        $query = $this->db->getQuery($query);
        $listOfExeJava = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $exeJava = new exeJava();
            $exeJava->setExeJava($result['id'], $result['idExeName'], $result['first'], $result['second'],
                $result['word'], $result['comment'], $result['working'], $result['mode'], $result['repeated'],
                $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect']);
            $listOfExeJava[] = $exeJava;
        }

        return $listOfExeJava;
    }

    public function getExeJavaSort(){
        $exeJavaNames = $this->exeJavaNameRepo->getExeJavaName();

        $listOfFull = array();

        foreach($exeJavaNames as $eEN){
            $exeJavaNameId = $eEN->getId();
            $listOfExeJava = $this->getExeJavaById($exeJavaNameId);
            foreach($listOfExeJava as $sE){
                $listOfFull[] = $sE;
            }
        }

        return $listOfFull;
    }

    public function prepareToShowExeJavaSort($showExeJava){
        $exeJavaName = $this->exeJavaNameRepo->getExeJavaName();

        foreach($showExeJava as $sEE){
            foreach($exeJavaName as $sEN){
                if($sEE->getIdExeName() == $sEN->getId()){
                    $sEE->setIdExeName($sEN->getName());
                    break;
                }
            }
        }

        return $showExeJava;
    }

    public function countWordsToEnd(){
        $exeJava = $this->getExeJavaJustToday();

        $allToEnd = 0;
        foreach($exeJava as $eE){
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
        $query = "SELECT COUNT(*) AS count FROM exejava WHERE done<>3;";
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

        $query = "SELECT COUNT(*) AS count FROM exejava WHERE first='".addslashes($first)."'";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        if($count != 0){
            showError("Takie ćwiczenie już prawdopodobnie istnieje!");
            return 0;
        }

        $query = "INSERT INTO exejava(idExeName, first, second, word, comment, working, mode, repeated, done, today, lastPerfectNum, lastPerfect) VALUES(:idExeName, :first, :second, :word, :comment, :working, :mode, 0,0,0,0,0);";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':idExeName', $idExeName);
        $stmt->bindParam(':first', $first);
        $stmt->bindParam(':second', $second);
        $stmt->bindParam(':word', $word);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':working', $working);
        $stmt->bindParam(':mode', $mode);
        $stmt->execute();

        $this->worksRepo->updateCountDone(10);

    }

    public function editFirst($id, $first){
        $query = "UPDATE exejava SET first=:newFirst WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newFirst', $first);
        $stmt->execute();
    }

    public function editSecond($id, $second){
        $query = "UPDATE exejava SET second=:newSecond WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newSecond', $second);
        $stmt->execute();
    }

    public function editWord($id, $word){
        $query = "UPDATE exejava SET word=:newWord WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newWord', $word);
        $stmt->execute();
    }

    public function editComment($id, $comment){
        $query = "UPDATE exejava SET comment=:newComment WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newComment', $comment);
        $stmt->execute();
    }

    public function editWorking($id, $working){
        $query = "UPDATE exejava SET working=:newWorking WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newWorking', $working);
        $stmt->execute();
    }

    public function editMode($id, $mode){
        $query = "UPDATE exejava SET mode=:newMode WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newMode', $mode);
        $stmt->execute();
    }

    public function editExeName($id, $idExeName){
        $query = "UPDATE exejava SET idExeName=:newIdExeName WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newIdExeName', $idExeName);
        $stmt->execute();
    }

    public function breakRepeatWords()
    {
        $query = "UPDATE exejava SET today = 0, done = 0  WHERE today = 1 AND (done=1 OR done=2);";
        $this->db->getQuery($query);
        $query = "UPDATE exejava SET today = 0 WHERE today = 1;";
        $this->db->getQuery($query);
        $this->configRepo->setConfig('repeatStartedExeJava', '0');
    }

    public function resetAllWords(){
        $query = "UPDATE exejava SET done = 0;";
        $this->db->getQuery($query);
    }

    public function resetWordsWithRange($from, $to){
        if(!empty($to)) {
            $query = "UPDATE exejava SET done = 0 WHERE idExeName BETWEEN '" . $from . "' AND '" . $to . "'";
            $this->db->getQuery($query);
            showMessage("Zresetowano ćwiczenia z zakresu: ".$from."- ".$to);
        }
        else{
            $idExeNames = $this->selectIdFromRange($from);
            foreach($idExeNames as $eN) {
                $query = "UPDATE exejava SET done = 0 WHERE idExeName BETWEEN '" . $eN . "' AND '" . $eN . "'";
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
        $query = "SELECT * FROM exejava WHERE today=1 AND lastPerfectNum > 4;";
        $query = $this->db->getQuery($query);

        while($result = $query->fetch(PDO::FETCH_BOTH))
        {
            $query2 = "UPDATE exejava SET lastPerfect=1 WHERE id=".$result['id'];
            $this->db->getQuery($query2);
        }

        $query = "UPDATE exejava SET done=0 WHERE today = 1 AND done <> 3;";
        $this->db->getQuery($query);

        $query = "UPDATE exejava SET today = 0, lastPerfectNum=0 WHERE today = 1;";
        $this->db->getQuery($query);

        $this->configRepo->setConfig('todayStartedExeJava', '0');

    }

    public function setRepeatWords($from, $to, $countEachExe){
        if(!empty($to)) {
            $query = "SELECT * FROM exejava WHERE idExeName BETWEEN '".$from."' AND '".$to."';";
            $query = $this->db->getQuery($query);
            while($result = $query->fetch(PDO::FETCH_BOTH))
            {
                $query2 = "UPDATE exejava SET today=1 WHERE done = 0 AND idExeName=".$result['idExeName']." ORDER BY RAND() LIMIT ".$countEachExe.";";
                $this->db->getQuery($query2);
            }
        }else{
            $idExeNames = $this->selectIdFromRange($from);
            foreach($idExeNames as $eN) {
                $query = "UPDATE exejava SET today=1 WHERE done = 0 AND idExeName BETWEEN '" . $eN . "' AND '" . $eN . "' ORDER BY RAND() LIMIT ".$countEachExe.";";
                $this->db->getQuery($query);
            }
        }

        $this->configRepo->setConfig('repeatStartedExeJava', '1');
    }


    public function setTodayWords($countAllWords){

        $query = "UPDATE exejava SET today = 1, done = 0, lastPerfect=0, lastPerfectNum=0 WHERE lastPerfect = 1;";
        $this->db->getQuery($query);

        $query = "UPDATE exejava SET today = 1 WHERE today = 0 AND done=0 ORDER BY RAND() LIMIT ".$countAllWords.";";
        $this->db->getQuery($query);

        $this->configRepo->setConfig('todayStartedExeJava', '1');
    }

    public function showTodayWord(){
        $query = "SELECT * FROM exejava WHERE today=1 AND done <> 3 ORDER BY RAND() LIMIT 1;";
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
                $query = "SELECT * FROM exejava WHERE id <> ".$id." AND word='".$word."' AND second <> '".$second."' ORDER BY RAND() LIMIT 3;";
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
            $query = "SELECT * FROM exejava WHERE  id = ".$id." AND second='".$answer."';";
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
            $query = "SELECT * FROM exejava WHERE  id = ".$id." AND first='".$answer."';";
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
        $repeatStarted = $this->configRepo->getConfig('repeatStartedExeJava');
        $todayStarted = $this->configRepo->getConfig('todayStartedExeJava');

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
                $query = "UPDATE exejava SET lastPerfectNum = lastPerfectNum + 1 WHERE id = ".$id.";";
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
        $query = "UPDATE exejava SET done = ".$done." WHERE id = ".$id.";";
        $this->db->getQuery($query);
    }

    public function updateRepeatedOneMore($id){
        $query = "UPDATE exejava SET repeated = repeated+1 WHERE id = ".$id.";";
        $this->db->getQuery($query);

        $query = "UPDATE note SET javaExe = javaExe + 1 WHERE date = CURDATE();";
        $this->db->getQuery($query);

        $this->worksRepo->updateCountDone(9);
    }

}