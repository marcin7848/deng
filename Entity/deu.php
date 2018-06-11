<?php

class deu
{
    private $id;
    private $polish;
    private $deutsch;
    private $repeated;
    private $done;
    private $today;
    private $lastPerfectNum;
    private $lastPerfect;
    private $thisUnit;

    public function setDeu($id, $polish, $deutsch, $repeated, $done, $today, $lastPerfectNum, $lastPerfect, $thisUnit)
    {
        $this->id = $id;
        $this->polish = $polish;
        $this->deutsch = $deutsch;
        $this->repeated = $repeated;
        $this->done = $done;
        $this->today = $today;
        $this->lastPerfectNum = $lastPerfectNum;
        $this->lastPerfect = $lastPerfect;
        $this->thisUnit = $thisUnit;
    }

    public function setDeuCopy($deu){
        return $deu;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPolish()
    {
        return $this->polish;
    }

    public function setPolish($polish)
    {
        $this->polish = $polish;
    }

    public function getDeutsch()
    {
        return $this->deutsch;
    }

    public function setDeutsch($deutsch)
    {
        $this->deutsch = $deutsch;
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

    public function getThisUnit()
    {
        return $this->thisUnit;
    }

    public function setThisUnit($thisUnit)
    {
        $this->thisUnit = $thisUnit;
    }

}