<?php

class irVerbsEng
{
    private $id;
    private $pl;
    private $inf;
    private $past;
    private $pastPart;
    private $done;
    private $repeated;

    public function setIrVerbsEng($id, $pl, $inf, $past, $pastPart, $done, $repeated)
    {
        $this->id = $id;
        $this->pl = $pl;
        $this->inf = $inf;
        $this->past = $past;
        $this->pastPart = $pastPart;
        $this->done = $done;
        $this->repeated = $repeated;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPl()
    {
        return $this->pl;
    }

    public function setPl($pl)
    {
        $this->pl = $pl;
    }

    public function getInf()
    {
        return $this->inf;
    }

    public function setInf($inf)
    {
        $this->inf = $inf;
    }

    public function getPast()
    {
        return $this->past;
    }

    public function setPast($past)
    {
        $this->past = $past;
    }

    public function getPastPart()
    {
        return $this->pastPart;
    }

    public function setPastPart($pastPart)
    {
        $this->pastPart = $pastPart;
    }

    public function getDone()
    {
        return $this->done;
    }

    public function setDone($done)
    {
        $this->done = $done;
    }

    public function getRepeated()
    {
        return $this->repeated;
    }

    public function setRepeated($repeated)
    {
        $this->repeated = $repeated;
    }


}