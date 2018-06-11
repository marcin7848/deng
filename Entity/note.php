<?php

class note
{
    private $id;
    private $date;
    private $eng;
    private $engExe;
    private $deu;
    private $deuExe;
    private $javaExe;
    private $done;
    private $justification;

    public function setNote($id, $date, $eng, $engExe, $deu, $deuExe, $javaExe, $done, $justification)
    {
        $this->id = $id;
        $this->date = $date;
        $this->eng = $eng;
        $this->engExe = $engExe;
        $this->deu = $deu;
        $this->deuExe = $deuExe;
        $this->javaExe = $javaExe;
        $this->done = $done;
        $this->justification = $justification;
    }

    public function setNoteCopy($note){
        return $note;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getEng()
    {
        return $this->eng;
    }

    public function setEng($eng)
    {
        $this->eng = $eng;
    }

    public function getEngExe()
    {
        return $this->engExe;
    }

    public function setEngExe($engExe)
    {
        $this->engExe = $engExe;
    }

    public function getDeu()
    {
        return $this->deu;
    }

    public function setDeu($deu)
    {
        $this->deu = $deu;
    }

    public function getDeuExe()
    {
        return $this->deuExe;
    }

    public function setDeuExe($deuExe)
    {
        $this->deuExe = $deuExe;
    }

    public function getJavaExe()
    {
        return $this->javaExe;
    }

    public function setJavaExe($javaExe)
    {
        $this->javaExe = $javaExe;
    }

    public function getDone()
    {
        return $this->done;
    }

    public function setDone($done)
    {
        $this->done = $done;
    }

    public function getJustification()
    {
        return $this->justification;
    }

    public function setJustification($justification)
    {
        $this->justification = $justification;
    }


}