<?php

class exeDeu
{
    private $id;
    private $idExeName;
    private $first;
    private $second;
    private $word;
    private $comment;
    private $working;
    private $mode;
    private $repeated;
    private $done;
    private $today;
    private $lastPerfectNum;
    private $lastPerfect;

    public function setExeDeu($id, $idExeName, $first, $second, $word, $comment, $working, $mode, $repeated, $done, $today, $lastPerfectNum, $lastPerfect)
    {
        $this->id = $id;
        $this->idExeName = $idExeName;
        $this->first = $first;
        $this->second = $second;
        $this->word = $word;
        $this->comment = $comment;
        $this->working = $working;
        $this->mode = $mode;
        $this->repeated = $repeated;
        $this->done = $done;
        $this->today = $today;
        $this->lastPerfectNum = $lastPerfectNum;
        $this->lastPerfect = $lastPerfect;
    }

    public function setExeDeuCopy($exeEng){
        return $exeEng;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIdExeName()
    {
        return $this->idExeName;
    }

    public function setIdExeName($idExeName)
    {
        $this->idExeName = $idExeName;
    }

    public function getFirst()
    {
        return $this->first;
    }

    public function setFirst($first)
    {
        $this->first = $first;
    }

    public function getSecond()
    {
        return $this->second;
    }

    public function setSecond($second)
    {
        $this->second = $second;
    }

    public function getWord()
    {
        return $this->word;
    }

    public function setWord($word)
    {
        $this->word = $word;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getWorking()
    {
        return $this->working;
    }

    public function setWorking($working)
    {
        $this->working = $working;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function getRepeated()
    {
        return $this->repeated;
    }

    public function setRepeated($repeated)
    {
        $this->repeated = $repeated;
    }

    public function getDone()
    {
        return $this->done;
    }

    public function setDone($done)
    {
        $this->done = $done;
    }

    public function getToday()
    {
        return $this->today;
    }

    public function setToday($today)
    {
        $this->today = $today;
    }

    public function getLastPerfectNum()
    {
        return $this->lastPerfectNum;
    }

    public function setLastPerfectNum($lastPerfectNum)
    {
        $this->lastPerfectNum = $lastPerfectNum;
    }

    public function getLastPerfect()
    {
        return $this->lastPerfect;
    }

    public function setLastPerfect($lastPerfect)
    {
        $this->lastPerfect = $lastPerfect;
    }

}