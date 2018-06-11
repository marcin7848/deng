<?php

require_once './db_connect.php';
require_once './Entity/exeDeu.php';
require_once './Model/configRepo.php';
require_once './Model/worksRepo.php';
require_once './Model/exeDeuNameRepo.php';

class exeDeuRepo
{
    private $db;
    private $configRepo;
    private $worksRepo;
    private $exeDeuNameRepo;

    public function __construct()
    {
        $this->db = new db_connect();
        $this->configRepo = new configRepo();
        $this->worksRepo = new worksRepo();
        $this->exeDeuNameRepo = new exeDeuNameRepo();
    }


    public function getExeDeu(){
        $query = "SELECT * FROM exedeu;";
        $query = $this->db->getQuery($query);
        $listOfExeDeu = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $exeDeu = new exeDeu();
            $exeDeu->setExeDeu($result['id'], $result['idExeName'], $result['first'], $result['second'],
                $result['word'], $result['comment'], $result['working'], $result['mode'], $result['repeated'],
                $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect']);
            $listOfExeDeu[] = $exeDeu;
        }

        return $listOfExeDeu;
    }

    public function getExeDeuById($idExeName){
        $query = "SELECT * FROM exedeu WHERE idExeName='".$idExeName."';";
        $query = $this->db->getQuery($query);
        $listOfExeDeu = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $exeDeu = new exeDeu();
            $exeDeu->setExeDeu($result['id'], $result['idExeName'], $result['first'], $result['second'],
                $result['word'], $result['comment'], $result['working'], $result['mode'], $result['repeated'],
                $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect']);
            $listOfExeDeu[] = $exeDeu;
        }

        return $listOfExeDeu;
    }

    public function getExeOne($id){
        $query = "SELECT * FROM exedeu WHERE id=".$id.";";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $exeDeu = new exeDeu();
        $exeDeu->setExeDeu($result['id'], $result['idExeName'], $result['first'], $result['second'],
            $result['word'], $result['comment'], $result['working'], $result['mode'], $result['repeated'],
            $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect']);

        return $exeDeu;
    }

    public function getExeDeuJustToday(){
        $query = "SELECT * FROM exedeu WHERE today=1;";
        $query = $this->db->getQuery($query);
        $listOfExeDeu = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $exeDeu = new exeDeu();
            $exeDeu->setExeDeu($result['id'], $result['idExeName'], $result['first'], $result['second'],
                $result['word'], $result['comment'], $result['working'], $result['mode'], $result['repeated'],
                $result['done'], $result['today'], $result['lastPerfectNum'], $result['lastPerfect']);
            $listOfExeDeu[] = $exeDeu;
        }

        return $listOfExeDeu;
    }

    public function getExeDeuSort(){
        $exeDeuNames = $this->exeDeuNameRepo->getExeDeuName();

        $listOfFull = array();

        foreach($exeDeuNames as $eEN){
            $exeDeuNameId = $eEN->getId();
            $listOfExeDeu = $this->getExeDeuById($exeDeuNameId);
            foreach($listOfExeDeu as $sE){
                $listOfFull[] = $sE;
            }
        }

        return $listOfFull;
    }

    public function prepareToShowExeDeuSort($showExeDeu){
        $exeDeuName = $this->exeDeuNameRepo->getExeDeuName();

        foreach($showExeDeu as $sEE){
            foreach($exeDeuName as $sEN){
                if($sEE->getIdExeName() == $sEN->getId()){
                    $sEE->setIdExeName($sEN->getName());
                    break;
                }
            }
        }

        return $showExeDeu;
    }

    public function countWordsToEnd(){
        $exeDeu = $this->getExeDeuJustToday();

        $allToEnd = 0;
        foreach($exeDeu as $eE){
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
        $query = "SELECT COUNT(*) AS count FROM exedeu WHERE done<>3;";
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

        $query = "SELECT COUNT(*) AS count FROM exedeu WHERE first='".addslashes($first)."'";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $count = $result['count'];
        if($count != 0){
            showError("Takie ćwiczenie już prawdopodobnie istnieje!");
            return 0;
        }

        $query = "INSERT INTO exedeu(idExeName, first, second, word, comment, working, mode, repeated, done, today, lastPerfectNum, lastPerfect) VALUES(:idExeName, :first, :second, :word, :comment, :working, :mode, 0,0,0,0,0);";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':idExeName', $idExeName);
        $stmt->bindParam(':first', $first);
        $stmt->bindParam(':second', $second);
        $stmt->bindParam(':word', $word);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':working', $working);
        $stmt->bindParam(':mode', $mode);
        $stmt->execute();

        $this->worksRepo->updateCountDone(8);


        global $activateFromGirls;
        if($activateFromGirls == 1){

            $query = "SELECT * FROM exedeuname WHERE id=".$idExeName;
            $query = $this->db->getQuery($query);
            $result = $query->fetch(PDO::FETCH_BOTH);
            $exeName = $result['name'];

            sendFromGirls("exedeu" , $exeName, $first, $second, $word, $comment, $working, $mode);
        }

    }

    public function editFirst($id, $first){
        $query = "UPDATE exedeu SET first=:newFirst WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newFirst', $first);
        $stmt->execute();
    }

    public function editSecond($id, $second){
        $query = "UPDATE exedeu SET second=:newSecond WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newSecond', $second);
        $stmt->execute();
    }

    public function editWord($id, $word){
        $query = "UPDATE exedeu SET word=:newWord WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newWord', $word);
        $stmt->execute();
    }

    public function editComment($id, $comment){
        $query = "UPDATE exedeu SET comment=:newComment WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newComment', $comment);
        $stmt->execute();
    }

    public function editWorking($id, $working){
        $query = "UPDATE exedeu SET working=:newWorking WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newWorking', $working);
        $stmt->execute();
    }

    public function editMode($id, $mode){
        $query = "UPDATE exedeu SET mode=:newMode WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newMode', $mode);
        $stmt->execute();
    }

    public function editExeName($id, $idExeName){
        $query = "UPDATE exedeu SET idExeName=:newIdExeName WHERE id='".$id."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':newIdExeName', $idExeName);
        $stmt->execute();
    }

    public function breakRepeatWords()
    {
        $query = "UPDATE exedeu SET today = 0, done = 0  WHERE today = 1 AND (done=1 OR done=2);";
        $this->db->getQuery($query);
        $query = "UPDATE exedeu SET today = 0 WHERE today = 1;";
        $this->db->getQuery($query);
        $this->configRepo->setConfig('repeatStartedExeDeu', '0');
    }

    public function resetAllWords(){
        $query = "UPDATE exedeu SET done = 0;";
        $this->db->getQuery($query);
    }

    public function resetWordsWithRange($from, $to){
        if(!empty($to)) {
            $query = "UPDATE exedeu SET done = 0 WHERE idExeName BETWEEN '" . $from . "' AND '" . $to . "'";
            $this->db->getQuery($query);
            showMessage("Zresetowano ćwiczenia z zakresu: ".$from."- ".$to);
        }
        else{
            $idExeNames = $this->selectIdFromRange($from);
            foreach($idExeNames as $eN) {
                $query = "UPDATE exedeu SET done = 0 WHERE idExeName BETWEEN '" . $eN . "' AND '" . $eN . "'";
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
        $query = "SELECT * FROM exedeu WHERE today=1 AND lastPerfectNum > 4;";
        $query = $this->db->getQuery($query);

        while($result = $query->fetch(PDO::FETCH_BOTH))
        {
            $query2 = "UPDATE exedeu SET lastPerfect=1 WHERE id=".$result['id'];
            $this->db->getQuery($query2);
        }

        $query = "UPDATE exedeu SET done=0 WHERE today = 1 AND done <> 3;";
        $this->db->getQuery($query);

        $query = "UPDATE exedeu SET today = 0, lastPerfectNum=0 WHERE today = 1;";
        $this->db->getQuery($query);

        $this->configRepo->setConfig('todayStartedExeDeu', '0');

    }

    public function setRepeatWords($from, $to, $countEachExe){
        if(!empty($to)) {
            $query = "SELECT * FROM exedeu WHERE idExeName BETWEEN '".$from."' AND '".$to."';";
            $query = $this->db->getQuery($query);
            while($result = $query->fetch(PDO::FETCH_BOTH))
            {
                $query2 = "UPDATE exedeu SET today=1 WHERE done = 0 AND idExeName=".$result['idExeName']." ORDER BY RAND() LIMIT ".$countEachExe.";";
                $this->db->getQuery($query2);
            }
        }else{
            $idExeNames = $this->selectIdFromRange($from);
            foreach($idExeNames as $eN) {
                $query = "UPDATE exedeu SET today=1 WHERE done = 0 AND idExeName BETWEEN '" . $eN . "' AND '" . $eN . "' ORDER BY RAND() LIMIT ".$countEachExe.";";
                $this->db->getQuery($query);
            }
        }

        $this->configRepo->setConfig('repeatStartedExeDeu', '1');
    }


    public function setTodayWords($countAllWords){
        $query = "UPDATE exedeu SET today = 1, done = 0, lastPerfect=0, lastPerfectNum=0 WHERE lastPerfect = 1;";
        $this->db->getQuery($query);

        $query = "UPDATE exedeu SET today = 1 WHERE today = 0 AND done=0 ORDER BY RAND() LIMIT ".$countAllWords.";";
        $this->db->getQuery($query);

        $this->configRepo->setConfig('todayStartedExeDeu', '1');
    }

    public function showTodayWord(){
        $query = "SELECT * FROM exedeu WHERE today=1 AND done <> 3 ORDER BY RAND() LIMIT 1;";
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
                $query = "SELECT * FROM exedeu WHERE id <> ".$id." AND word='".$word."' AND second <> '".$second."' ORDER BY RAND() LIMIT 3;";
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
            $query = "SELECT * FROM exedeu WHERE  id = ".$id." AND second='".$answer."';";
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
            $query = "SELECT * FROM exedeu WHERE  id = ".$id." AND first='".$answer."';";
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
        $repeatStarted = $this->configRepo->getConfig('repeatStartedExeDeu');
        $todayStarted = $this->configRepo->getConfig('todayStartedExeDeu');

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
                $query = "UPDATE exedeu SET lastPerfectNum = lastPerfectNum + 1 WHERE id = ".$id.";";
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
        $query = "UPDATE exedeu SET done = ".$done." WHERE id = ".$id.";";
        $this->db->getQuery($query);
    }

    public function updateRepeatedOneMore($id){
        $query = "UPDATE exedeu SET repeated = repeated+1 WHERE id = ".$id.";";
        $this->db->getQuery($query);

        $query = "UPDATE note SET deuExe = deuExe + 1 WHERE date = CURDATE();";
        $this->db->getQuery($query);

        $this->worksRepo->updateCountDone(7);
    }

}