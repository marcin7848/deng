<?php

require_once './Model/noteRepo.php';
require_once './Model/configRepo.php';
require_once './Model/worksRepo.php';

class controllerNote
{
    private $noteRepo;
    private $configRepo;
    private $worksRepo;

    public function __construct()
    {
        $this->noteRepo = new noteRepo();
        $this->configRepo = new configRepo();
        $this->worksRepo = new worksRepo();

        $this->controller();

        $note = $this->noteRepo->getNote();
        $showNote = array();

        if(count($note) >= 12) {
            for ($i = count($note) - 12; $i < count($note); $i++) {
                $showNote[] = $note[$i];
            }
        }
        else{
            $showNote = $note;
        }

        $sumRepeatedEng = $this->noteRepo->sumRepeated('eng');
        $sumRepeatedEngExe = $this->noteRepo->sumRepeated('engExe');
        $sumRepeatedDeu = $this->noteRepo->sumRepeated('deu');
        $sumRepeatedDeuExe = $this->noteRepo->sumRepeated('deuExe');
        $sumRepeatedJavaExe = $this->noteRepo->sumRepeated('javaExe');

        $sumWorksPoints = $this->worksRepo->sumWorksPoints();
        $worksPointsWithoutBreak = $this->configRepo->getConfig('worksPointsWithoutBreak');

        $showWorks = $this->worksRepo->getActivatedWorks();

        require_once './view/note.html';

    }

    private function controller(){
        if(isset($_POST['justificationId']))
        {
            $this->noteRepo->putJustification($_POST['justificationId'], $_POST['justificationText']);
        }

        if(isset($_POST['add7Days'])){
            $this->noteRepo->add7Days();
        }
        if(isset($_GET['logout'])){
            unset($_SESSION['login']);
            session_destroy();
            header("Location: index.php");
        }
    }

}