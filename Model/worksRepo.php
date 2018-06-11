<?php
require_once './db_connect.php';
require_once './Entity/works.php';
require_once './Model/configRepo.php';

class worksRepo
{
    private $db;
    private $configRepo;

    public function __construct()
    {
        $this->db = new db_connect();
        $this->configRepo = new configRepo();
    }

    public function getWorks()
    {
        $query = "SELECT * FROM works;";
        $query = $this->db->getQuery($query);
        $listOfWorks = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $works = new works();
            $works->setWorks($result['id'], $result['description'], $result['activated'], $result['count'],
                $result['countDone'], $result['points']);
            $listOfWorks[] = $works;
        }

        return $listOfWorks;
    }

    public function getActivatedWorks()
    {
        $query = "SELECT * FROM works WHERE activated=1;";
        $query = $this->db->getQuery($query);
        $listOfWorks = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $works = new works();
            $works->setWorks($result['id'], $result['description'], $result['activated'], $result['count'],
                $result['countDone'], $result['points']);
            $listOfWorks[] = $works;
        }

        return $listOfWorks;
    }


    public function checkWorksDate(){
        $worksDate = $this->configRepo->getConfig('worksDate');

        $now = new DateTime();
        $now->setTime(0, 0);
        $nowDate = $now->format('Y-m-d');

        if($worksDate < $nowDate){
            $this->resetWorks();
        }

    }

    public function sumWorksPoints(){
        $query = "SELECT SUM(points) AS sum FROM works;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        return $result['sum'];
    }

    public function resetWorks(){
        $works = $this->getActivatedWorks();

        $countAllWorks = 0;
        $countWorksDone = 0;
        foreach($works as $work){
            $countAllWorks++;
            if($work->getCountDone() >= $work->getCount()){
                $countWorksDone++;
            }
        }

        if($countAllWorks != $countWorksDone){
            $this->configRepo->setConfig('worksPointsWithoutBreak', '0');
        }

        foreach($works as $work){
            $id = $work->getId();
            $count = $work->getCount();
            $countDone = $work->getCountDone();
            $points = 0;

            $points += $countDone * $this->scalePoints($id);

            if($countDone >= $count){
                $points += 10;
            }

            if($countAllWorks == $countWorksDone){
                $points += 30;
                $worksPointsWithoutBreak = $this->configRepo->getConfig('worksPointsWithoutBreak');
                $worksPointsWithoutBreak += $points;
                $this->configRepo->setConfig('worksPointsWithoutBreak', round($worksPointsWithoutBreak, 2));
            }

            $this->updatePoints($id, $points);
        }

        $this->clearWorks();

        //te co zakmentowane oznaczają, że będą nie tylko eng i dodawanie eng
        //$countOfNewWorks = mt_rand(3, 6);
        $countOfNewWorks = 2;
        
        //$arrayOfProbabilityId = array(1,1,1,1,1,1,1,2,2,2,2,3,3,3,4,4,4,5,5,6,6,7,8,9,10,11,12);
        $arrayOfProbabilityId = array(1,2);
        
        shuffle($arrayOfProbabilityId);
        shuffle($arrayOfProbabilityId);

        $arrayOfNewWorksId = array();

        while(count($arrayOfNewWorksId) != $countOfNewWorks){
            $key =  array_rand($arrayOfProbabilityId);
            $arrayOfNewWorksId[] = $arrayOfProbabilityId[$key];
            $arrayOfNewWorksId = array_unique($arrayOfNewWorksId);
        }

        foreach($arrayOfNewWorksId as $id){
            $count = $this->randCountOfWords($id);
            $this->startWork($id, $count);
        }

        $now = new DateTime();
        $now->setTime(0, 0);
        $nowDate = $now->format('Y-m-d');

        $this->configRepo->setConfig('worksDate', $nowDate);

    }

    public function scalePoints($id){
        if($id == 1){
            return 0.4; //eng
        }
        if($id == 2){
            return 0.7; //dodaj eng
        }
        if($id == 3){
            return 0.9; //engExe
        }
        if($id == 4){
            return 1.2; //dodaj engExe
        }
        if($id == 5){
            return 0.7; //deu
        }
        if($id == 6){
            return 0.6; //dodaj deu
        }
        if($id == 7){
            return 0.9; //deu exe
        }
        if($id == 8){
            return 1; //dodaj deu exe
        }
        if($id == 9){
            return 1; //java
        }
        if($id == 10){
            return 1; //dodaj java
        }
        if($id == 11){
            return 15; //nieregularne eng
        }
        if($id == 12){
            return 15; //nieregularne deu
        }
    }

    public function randCountOfWords($id){
        if($id == 1){
            return mt_rand(40, 120); //eng
        }
        if($id == 2){
            return mt_rand(15, 30); //dodaj eng
        }
        if($id == 3){
            return mt_rand(10, 20); //engExe
        }
        if($id == 4){
            return mt_rand(10, 20); //dodaj engExe
        }
        if($id == 5){
            return mt_rand(20, 30); //deu
        }
        if($id == 6){
            return mt_rand(10, 20); //dodaj deu
        }
        if($id == 7){
            return mt_rand(5, 10); //deu exe
        }
        if($id == 8){
            return mt_rand(5, 10); //dodaj deu exe
        }
        if($id == 9){
            return mt_rand(5, 10); //java
        }
        if($id == 10){
            return mt_rand(5, 10); //dodaj java
        }
        if($id == 11){
            return 1; //nieregularne eng
        }
        if($id == 12){
            return 1; //nieregularne deu
        }

    }

    public function updatePoints($id, $points){
        $query = "UPDATE works SET points = round(points + ".$points.", 2) WHERE id='".$id."'";
        $this->db->getQuery($query);
    }

    public function clearWorks(){
        $query = "UPDATE works SET activated=0, count=0, countDone=0;";
        $this->db->getQuery($query);
    }

    public function startWork($id, $count){
        $query = "UPDATE works SET activated=1, count=".$count." WHERE id='".$id."'";
        $this->db->getQuery($query);
    }

    public function updateCountDone($id){
        $query = "UPDATE works SET countDone=countDone+1 WHERE id='".$id."';";
        $this->db->getQuery($query);
    }

}