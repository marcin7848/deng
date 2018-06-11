<?php

class works
{
    private $id;
    private $description;
    private $activated;
    private $count;
    private $countDone;
    private $points;

    public function setWorks($id, $description, $activated, $count, $countDone, $points)
    {
        $this->id = $id;
        $this->description = $description;
        $this->activated = $activated;
        $this->count = $count;
        $this->countDone = $countDone;
        $this->points = $points;
    }


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getActivated()
    {
        return $this->activated;
    }

    public function setActivated($activated)
    {
        $this->activated = $activated;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function setCount($count)
    {
        $this->count = $count;
    }

    public function getCountDone()
    {
        return $this->countDone;
    }

    public function setCountDone($countDone)
    {
        $this->countDone = $countDone;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function setPoints($points)
    {
        $this->points = $points;
    }
}