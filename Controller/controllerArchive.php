<?php

class controllerArchive
{
    private $noteRepo;

    public function __construct()
    {
        $this->noteRepo = new noteRepo();

        $showNote = $this->noteRepo->getNote();

        require_once './view/archive.html';
    }
}