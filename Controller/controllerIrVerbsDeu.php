<?php

require_once './Model/irVerbsDeuRepo.php';
require_once './Model/configRepo.php';

class controllerIrVerbsDeu
{
    private $irVerbsDeuRepo;
    private $configRepo;

    public function __construct()
    {
        $this->irVerbsDeuRepo = new irVerbsDeuRepo();
        $this->configRepo = new configRepo();

        $getCountToEnd = $this->irVerbsDeuRepo->getCountToEnd();

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


                $checked = $this->irVerbsDeuRepo->checkAnswerToday($_POST['id'], $_POST['inf'], $_POST['past'], $_POST['pastPart']);

                $this->irVerbsDeuRepo->saveAnswerToday($_POST['id'], $checked);

                $tab = $this->irVerbsDeuRepo->getIrVerbsDeuOne($_POST['id']);

            }
            else{
                $showWord = $this->irVerbsDeuRepo->showTodayVerb();
            }

            require_once './view/irVerbsDeuToday.html';
            return 0;
        }

        $this->controller();
        $getCountToEnd = $this->irVerbsDeuRepo->getCountToEnd();
        $show = $this->irVerbsDeuRepo->getIrVerbsDeu();

        require_once './view/irVerbsDeu.html';
    }

    public function controller(){
        if (isset($_POST['addNewIrVerb'])) {
            $this->irVerbsDeuRepo->addNewIrVerb($_POST['pl'], $_POST['inf'], $_POST['past'], $_POST['pastPart']);
        }
        if (isset($_POST['resetAllVerbsDeu'])) {
            $this->irVerbsDeuRepo->resetAllVerbs();
        }

    }
}