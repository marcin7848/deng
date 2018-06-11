<?php
/* Kopiowanie rekordów z jednej bazy do drugiej dla dziewczyn */

echo 'Wylaczone!!!!';

/*
header('Content-type: text/html; charset=UTF-8');
set_time_limit(0);

require_once 'db_connect.php';

$db = new db_connect();

class db_connect2 {
    private $pdo;

    public function __construct(){
        try{
            global $host, $user, $pass;
            $this->pdo = new PDO('mysql:host='.$host.';dbname=deng2', $user, $pass);
            $this->pdo->exec("set names utf8");
        }catch(PDOException $e){
            exit('Połączenie nie mogło zostać utworzone: ' . $e->getMessage());
        }

    }

    public function getQuery($query){
        return $stmt = $this->pdo->query($query);
    }

    public function prepareQuery($query){
        return $stmt = $this->pdo->prepare($query);
    }

}


$db2 = new db_connect2();

$query = "SELECT * FROM eng;";
$query = $db2->getQuery($query);
while($result = $query->fetch(PDO::FETCH_BOTH)){
    $eng = $result['english'];
    $pl = $result['polish'];
    $query2 = "SELECT COUNT(*) AS count FROM eng WHERE english='".addslashes($eng)."'";
    $query2 = $db->getQuery($query2);
    $result2 = $query2->fetch(PDO::FETCH_BOTH);
    $count = $result2['count'];

    if($count == 0 ){
        $query3 = "INSERT INTO eng(polish, english, repeated, done, today, lastPerfectNum, lastPerfect, thisUnit) VALUES(:polish, :english, 0, 0, 0, 0, 0, 0);";
        $stmt = $db->prepareQuery($query3);
        $stmt->bindParam(':polish', $pl);
        $stmt->bindParam(':english', $eng);
        $stmt->execute();
    }
}


$query = "SELECT * FROM deu;";
$query = $db2->getQuery($query);
while($result = $query->fetch(PDO::FETCH_BOTH)){
    $deu = $result['deutsch'];
    $pl = $result['polish'];
    $query2 = "SELECT COUNT(*) AS count FROM deu WHERE deutsch='".addslashes($deu)."'";
    $query2 = $db->getQuery($query2);
    $result2 = $query2->fetch(PDO::FETCH_BOTH);
    $count = $result2['count'];

    if($count == 0 ){
        $query3 = "INSERT INTO deu(polish, deutsch, repeated, done, today, lastPerfectNum, lastPerfect, thisUnit) VALUES(:polish, :deutsch, 0, 0, 0, 0, 0, 0);";
        $stmt = $db->prepareQuery($query3);
        $stmt->bindParam(':polish', $pl);
        $stmt->bindParam(':deutsch', $deu);
        $stmt->execute();
    }
}


$query = "SELECT * FROM exeengname;";
$query = $db2->getQuery($query);
while($result = $query->fetch(PDO::FETCH_BOTH)){
    $name = $result['name'];
    $query2 = "SELECT COUNT(*) AS count FROM exeengname WHERE name='".addslashes($name)."'";
    $query2 = $db->getQuery($query2);
    $result2 = $query2->fetch(PDO::FETCH_BOTH);
    $count = $result2['count'];

    if($count == 0 ){
        $query3 = "INSERT INTO exeengname(name) VALUES(:newName);";
        $stmt = $db->prepareQuery($query3);
        $stmt->bindParam(':newName', $name);
        $stmt->execute();
    }
}


$query = "SELECT * FROM exedeuname;";
$query = $db2->getQuery($query);
while($result = $query->fetch(PDO::FETCH_BOTH)){
    $name = $result['name'];
    $query2 = "SELECT COUNT(*) AS count FROM exedeuname WHERE name='".addslashes($name)."'";
    $query2 = $db->getQuery($query2);
    $result2 = $query2->fetch(PDO::FETCH_BOTH);
    $count = $result2['count'];

    if($count == 0 ){
        $query3 = "INSERT INTO exedeuname(name) VALUES(:newName);";
        $stmt = $db->prepareQuery($query3);
        $stmt->bindParam(':newName', $name);
        $stmt->execute();
    }
}


$query = "SELECT * FROM exeeng;";
$query = $db2->getQuery($query);
while($result = $query->fetch(PDO::FETCH_BOTH)){
    $idExeName = $result['idExeName'];
    $first = $result['first'];
    $second = $result['second'];
    $word = $result['word'];
    $comment = $result['comment'];
    $working = $result['working'];
    $mode = $result['mode'];

    $query2 = "SELECT COUNT(*) AS count FROM exeeng WHERE first='".addslashes($first)."'";
    $query2 = $db->getQuery($query2);
    $result2 = $query2->fetch(PDO::FETCH_BOTH);
    $count = $result2['count'];

    if($count == 0 ){
        $query2 = "SELECT * FROM exeengname WHERE id='".$idExeName."'";
        $query2 = $db2->getQuery($query2);
        $result2 = $query2->fetch(PDO::FETCH_BOTH);
        $exeName_db2 = $result2['name'];

        $query2 = "SELECT * FROM exeengname WHERE name='".addslashes($exeName_db2)."'";
        $query2 = $db->getQuery($query2);
        $result2 = $query2->fetch(PDO::FETCH_BOTH);
        $idExeName_db = $result2['name'];

        $query = "INSERT INTO exeeng(idExeName, first, second, word, comment, working, mode, repeated, done, today, lastPerfectNum, lastPerfect) VALUES(:idExeName, :first, :second, :word, :comment, :working, :mode, 0,0,0,0,0);";
        $stmt = $db->prepareQuery($query);
        $stmt->bindParam(':idExeName', $idExeName);
        $stmt->bindParam(':first', $first);
        $stmt->bindParam(':second', $second);
        $stmt->bindParam(':word', $word);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':working', $working);
        $stmt->bindParam(':mode', $mode);
        $stmt->execute();


    }
}


$query = "SELECT * FROM exedeu;";
$query = $db2->getQuery($query);
while($result = $query->fetch(PDO::FETCH_BOTH)){
    $idExeName = $result['idExeName'];
    $first = $result['first'];
    $second = $result['second'];
    $word = $result['word'];
    $comment = $result['comment'];
    $working = $result['working'];
    $mode = $result['mode'];

    $query2 = "SELECT COUNT(*) AS count FROM exedeu WHERE first='".addslashes($first)."'";
    $query2 = $db->getQuery($query2);
    $result2 = $query2->fetch(PDO::FETCH_BOTH);
    $count = $result2['count'];

    if($count == 0 ){
        $query2 = "SELECT * FROM exedeuname WHERE id='".$idExeName."'";
        $query2 = $db2->getQuery($query2);
        $result2 = $query2->fetch(PDO::FETCH_BOTH);
        $exeName_db2 = $result2['name'];

        $query2 = "SELECT * FROM exedeuname WHERE name='".addslashes($exeName_db2)."'";
        $query2 = $db->getQuery($query2);
        $result2 = $query2->fetch(PDO::FETCH_BOTH);
        $idExeName_db = $result2['name'];

        $query = "INSERT INTO exedeu(idExeName, first, second, word, comment, working, mode, repeated, done, today, lastPerfectNum, lastPerfect) VALUES(:idExeName, :first, :second, :word, :comment, :working, :mode, 0,0,0,0,0);";
        $stmt = $db->prepareQuery($query);
        $stmt->bindParam(':idExeName', $idExeName);
        $stmt->bindParam(':first', $first);
        $stmt->bindParam(':second', $second);
        $stmt->bindParam(':word', $word);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':working', $working);
        $stmt->bindParam(':mode', $mode);
        $stmt->execute();


    }
}

*/