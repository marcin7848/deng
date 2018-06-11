<?php

class exeEngName
{
    private $id;
    private $name;

    public function setExeEngName($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function setExeEngNameCopy($exeEngName){
        return $exeEngName;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

}