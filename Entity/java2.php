<?php

class java2
{
    private $id;
    private $polish;
    private $function;
    private $repeated;
    private $done;
    private $today;
    private $lastPerfectNum;
    private $lastPerfect;
    private $thisUnit;

    public function setJava($id, $polish, $function, $repeated, $done, $today, $lastPerfectNum, $lastPerfect, $thisUnit)
    {
        $this->id = $id;
        $this->polish = $polish;
        $this->function = $function;
        $this->repeated = $repeated;
        $this->done = $done;
        $this->today = $today;
        $this->lastPerfectNum = $lastPerfectNum;
        $this->lastPerfect = $lastPerfect;
        $this->thisUnit = $thisUnit;
    }

    public function setJavaCopy($java){
        return $java;
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

    public function getFunction()
    {
        return $this->function;
    }


    public function setFunction($function)
    {
        $this->function = $function;
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