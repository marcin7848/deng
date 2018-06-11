<?php

require_once './Model/irVerbsEngRepo.php';
require_once './Model/configRepo.php';

class controllerIrVerbsEng
{
    private $irVerbsEngRepo;
    private $configRepo;

    public function __construct()
    {
        $this->irVerbsEngRepo = new irVerbsEngRepo();
        $this->configRepo = new configRepo();

        $getCountToEnd = $this->irVerbsEngRepo->getCountToEnd();

        if(isset($_GET['today'])){

            $showFirstBase = 0;
            $showWord = "";
            if(isset($_POST['sendAnswer'])){
                $showFirstBase = 1;

                if(empty($_POST['inf'])){
                    $_POST['inf'] = "0";
                }
                if(empty($_POST['past'])){
                    $_POST['past'] = "0";
                }
                if(empty($_POST['pastPart'])){
                    $_POST['pastPart'] = "0";
                }


                $checked = $this->irVerbsEngRepo->checkAnswerToday($_POST['id'], $_POST['inf'], $_POST['past'], $_POST['pastPart']);

                $this->irVerbsEngRepo->saveAnswerToday($_POST['id'], $checked);

                $tab = $this->irVerbsEngRepo->getIrVerbsEngOne($_POST['id']);

            }
            else{
                $showWord = $this->irVerbsEngRepo->showTodayVerb();
            }

            require_once './view/irVerbsEngToday.html';
            return 0;
        }

        $this->controller();
        $getCountToEnd = $this->irVerbsEngRepo->getCountToEnd();
        $show = $this->irVerbsEngRepo->getIrVerbsEng();

        require_once './view/irVerbsEng.html';
    }

    public function controller(){
        if (isset($_POST['addNewIrVerb'])) {
            $this->irVerbsEngRepo->addNewIrVerb($_POST['pl'], $_POST['inf'], $_POST['past'], $_POST['pastPart']);
        }
        if (isset($_POST['resetAllVerbsEng'])) {
            $this->irVerbsEngRepo->resetAllVerbs();
        }

    }
}