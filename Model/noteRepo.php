<?php

require_once './db_connect.php';
require_once './Entity/note.php';

class noteRepo
{
    private $db;

    public function __construct()
    {
        $this->db = new db_connect();
    }

    public function getNote()
    {
        $query = "SELECT * FROM note;";
        $query = $this->db->getQuery($query);
        $listOfNote = array();
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            $note = new note();
            $note->setNote($result['id'], $result['date'], $result['eng'], $result['engExe'],
                $result['deu'], $result['deuExe'], $result['javaExe'], $result['done'], $result['justification']);
            $listOfNote[] = $note;
        }

        return $listOfNote;
    }

    public function getStatusCell($date, $now, $done, $valueCell)
    {

        $statusCell = 0; //date > now, brak koloru

        if ($date < $now) {
            if ($done == 0) {
                if ($valueCell > 0) {
                    $statusCell = 3; //zrobione
                } else {
                    $statusCell = 1; //nie zrobione
                }
            } else {
                if ($valueCell > 0) {
                    $statusCell = 3; //zrobione
                } else
                    $statusCell = 2; //usprawiedliwione
            }
        } else {
            if ($valueCell > 0) {
                $statusCell = 3; //zrobione
            }
        }

        return $statusCell;
    }

    public function getColorCell($statusCell)
    {
        if ($statusCell == 0) { //data > now, brak koloru
            $color = "";
        } else if ($statusCell == 1) { //nie zrobione
            $color = "#FF0000";
        } else if ($statusCell == 2) { //usprawieliwione
            $color = "#CC9933";
        } else {
            $color = "#003300"; //zrobione
        }

        return $color;
    }

    public function showStatusCell($note)
    {
        $newNote = new note();
        $newNote = $newNote->setNoteCopy($note);

        $nowTime = new DateTime();

        if ($nowTime > $newNote->getDate()) {
            if(($newNote->getEng() == 0) || ($newNote->getEngExe() == 0) ||
                ($newNote->getDeu() == 0) || ($newNote->getDeuExe() == 0) ||
                ($newNote->getJavaExe() == 0))
            {
                if($newNote->getDone() == 0){
                    return 1;
                }
            }
        }

        return 0;
    }


    public function putJustification($noteId, $noteJustification){
        $query = "UPDATE note SET done='1', justification=:noteJustification WHERE id='".$noteId."'";
        $stmt = $this->db->prepareQuery($query);
        $stmt->bindParam(':noteJustification', $noteJustification);
        $stmt->execute();
    }

    public function add7Days(){
        $query = "SELECT * FROM note ORDER BY id desc LIMIT 1;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        $date = new DateTime($result['date']);
        $toAdd = new DateInterval('PT24H');

        for($i=1; $i<=7; $i++){
            $date->add($toAdd);
            $query = "INSERT INTO note(date, eng, engExe, deu, deuExe, javaExe, done, justification) VALUES('".$date->format('Y-m-d')."', 0, 0, 0, 0, 0, 0, '');";
            $this->db->getQuery($query);
        }


    }

    public function sumRepeated($name){
        $query = "SELECT SUM(".$name.") AS sum FROM note;";
        $query = $this->db->getQuery($query);
        $result = $query->fetch(PDO::FETCH_BOTH);
        return $result['sum'];
    }

    public function setDoneNotes(){
        $query = "UPDATE note SET done=1 WHERE done = 0 AND eng > 0 AND engExe > 0 AND deu > 0 AND deuExe >0 AND javaExe > 0";
        $this->db->getQuery($query);
    }

}