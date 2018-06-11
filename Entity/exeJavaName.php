<?php

class exeJavaName
{
    private $id;
    private $name;

    public function setExeJavaName($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function setExeJavaNameCopy($exeJavaName){
        return $exeJavaName;
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