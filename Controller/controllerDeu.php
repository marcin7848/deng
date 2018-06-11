<?php

require_once './Model/deuRepo.php';
require_once './Model/configRepo.php';

class controllerDeu
{
    private $deuRepo;
    private $configRepo;

    public function __construct()
    {
        $this->deuRepo = new deuRepo();
        $this->configRepo = new configRepo();

        $countWordsToEndDeu = $this->deuRepo->countWordsToEnd();

        if(isset($_GET['today'])){
            $repeatStartedDeu = $this->configRepo->getConfig('repeatStartedDeu');
            $todayStartedDeu = $this->configRepo->getConfig('todayStartedDeu');

            if(($repeatStartedDeu == 0) && ($todayStartedDeu == 0))
            {
                header("Location: index.php?deu");
            }

            $showFirstBase = 0;
            $showWord = "";
            $tab = NULL;
            if(isset($_POST['sendAnswer'])){
                $showFirstBase = 1;

                if(empty($_POST['answer'])){
                    $_POST['answer'] = "0";
                }

                $checkAnswerToday = $this->deuRepo->checkAnswerToday($_POST['id'], $_POST['wordSide'], addslashes($_POST['answer']));

                if($checkAnswerToday == 1){
                    $tab['color'] = "#009900";
                }
                else{
                    $tab['color'] = "#990000";
                }
                $deu = $this->deuRepo->getNoteOne($_POST['id']);
                $tab['id'] = $deu->getId();

                $tab['wordSide'] = $_POST['wordSide'];

                if($_POST['wordSide'] == 1){
                    $tab['word'] = $deu->getPolish();
                    $tab['answer'] = $deu->getDeutsch();
                }
                else{
                    $tab['word'] = $deu->getDeutsch();
                    $tab['answer'] = $deu->getPolish();
                }

                $this->deuRepo->saveAnswerToday($_POST['id'], $checkAnswerToday, $_POST['wordSide']);
                $countWordsToEndDeu = $this->deuRepo->countWordsToEnd();

            }
            else{
                $showFirstBase = 0;
                $showWord = $this->deuRepo->showTodayWord();
            }



            require_once './view/deuToday.html';
            return 0;
        }

        $this->controller();

        if(isset($_GET['show'])){
            $showDeu = $this->deuRepo->getDeu();
        }

        $todaysDeuWords = $this->configRepo->getConfig('todaysDeuWords');
        $thisUnitDeu = $this->configRepo->getConfig('thisUnitDeu');
        $workingDeu = $this->configRepo->getConfig('workingDeu');
        $modeDeu = $this->configRepo->getConfig('modeDeu');
        $repeatStartedDeu = $this->configRepo->getConfig('repeatStartedDeu');
        $todayStartedDeu = $this->configRepo->getConfig('todayStartedDeu');



        $getCountToEndAll = $this->deuRepo->getCountToEndAll();

        require_once './view/deu.html';

    }

    private function controller(){
        if(isset($_GET['changeThisUnit'])){
            $this->deuRepo->changeThisUnit($_GET['changeThisUnit']);
        }

        if(isset($_POST['editFirstWord'])){
            $this->deuRepo->editDeutsch($_POST['editFirstWord'], $_POST['editValueFirstWord']);
        }

        if(isset($_POST['editSecondWord'])){
            $this->deuRepo->editPolish($_POST['editSecondWord'], $_POST['editValueSecondWord']);
        }

        if(isset($_POST['addNewDeu'])){
            if(empty($_POST['addNewDeu']) || empty($_POST['addNewPl'])){
                showError('Uzupełnij oba pola przy dodawaniu słowa!');
            }
            else{
                $this->deuRepo->addNewWord($_POST['addNewPl'], $_POST['addNewDeu']);
            }
        }

        if(isset($_POST['todaysDeuWords'])){
            $this->configRepo->setConfig('todaysDeuWords', $_POST['todaysDeuWords']);

        }

        if(isset($_POST['changeThisUnitDeu'])){
            $thisUnitDeu = $this->configRepo->getConfig('thisUnitDeu');
            $newThisUnitDeu = ($thisUnitDeu == 1) ? 0 : 1;
            $this->configRepo->setConfig('thisUnitDeu', $newThisUnitDeu);
        }

        if(isset($_POST['deleteThisUnit'])){
            $this->deuRepo->deleteThisUnit();
            showMessage('Usunąłeś This Unit ze wszystkich słów!');
        }

        if(isset($_POST['changeWorkingDeu'])){
            $workingDeu = $this->configRepo->getConfig('workingDeu');
            if($workingDeu < 3){
                $workingDeu++;
            }
            else{
                $workingDeu = 1;
            }

            $this->configRepo->setConfig('workingDeu', $workingDeu);

            showMessage('Poprawnie zmieniono działanie deu!');
        }
        if(isset($_POST['changeModeDeu'])){
            $modeDeu = $this->configRepo->getConfig('modeDeu');
            if($modeDeu < 3){
                $modeDeu++;
            }
            else{
                $modeDeu = 1;
            }

            $this->configRepo->setConfig('modeDeu', $modeDeu);

            showMessage('Poprawnie zmieniono tryb deu!');
        }

        if(isset($_POST['resetAllWordsDeu'])){
            $repeatStartedDeu = $this->configRepo->getConfig('repeatStartedDeu');
            $todayStartedDeu = $this->configRepo->getConfig('todayStartedDeu');
            if(($repeatStartedDeu == 0) && ($todayStartedDeu == 0)) {
                $this->deuRepo->resetAllWords();
                showMessage('Zresetowano wszystkie słowa! Możesz już je powtarzać.');
            }
            else{
                showError('Nie możesz resetować słów, gdy uruchomiona jest jakaś powtórka!');
            }
        }

        if(isset($_POST['resetWordsWithRangeFrom'])){
            $repeatStartedDeu = $this->configRepo->getConfig('repeatStartedDeu');
            $todayStartedDeu = $this->configRepo->getConfig('todayStartedDeu');
            if(($repeatStartedDeu == 0) && ($todayStartedDeu == 0)) {
                $this->deuRepo->resetWordsWithRange($_POST['resetWordsWithRangeFrom'], $_POST['resetWordsWithRangeTo']);
                showMessage('Zresetowano słowa z zakresu: ' . $_POST["resetWordsWithRangeFrom"] . '-' . $_POST["resetWordsWithRangeTo"] . '');
            }
            else{
                showError('Nie możesz resetować słów, gdy uruchomiona jest jakaś powtórka!');
            }
        }


        if(isset($_POST['breakRepeatWordsDeu'])){
            $this->deuRepo->breakRepeatWords();
            showMessage('Przerwałeś powtórkę słów!');
        }

        if(isset($_POST['startRepeatWordsFrom'])){
            $repeatStartedDeu = $this->configRepo->getConfig('repeatStartedDeu');
            $todayStartedDeu = $this->configRepo->getConfig('todayStartedDeu');
            if(($repeatStartedDeu == 0) && ($todayStartedDeu == 0)) {
                if (empty($_POST['startRepeatWordsFrom']) OR empty($_POST['startRepeatWordsTo'])) {
                    showError('Musisz podać oba zakresy!');
                } else {
                    $this->deuRepo->setRepeatWords($_POST['startRepeatWordsFrom'], $_POST['startRepeatWordsTo']);
                    header("Location: index.php?deu&today");
                }
            }
            else{
                showError('Nie możesz uruchomić powtórki, jeżeli jest już jakaś uruchomiona!');
            }
        }

        if(isset($_POST['startTodayDeu'])){
            $repeatStartedDeu = $this->configRepo->getConfig('repeatStartedDeu');
            $todayStartedDeu = $this->configRepo->getConfig('todayStartedDeu');
            if($repeatStartedDeu == 0){
                if($todayStartedDeu == 0) {
                    $this->deuRepo->setTodayWords();
                }
                header("Location: index.php?deu&today");
            }
            else{
                showError('Nie możesz uruchomić powtórki, jeżeli jest już uruchomina inna!');
            }
        }

        if(isset($_POST['resetTodayWordsDeu'])){
            $repeatStartedDeu = $this->configRepo->getConfig('repeatStartedDeu');
            $todayStartedDeu = $this->configRepo->getConfig('todayStartedDeu');
            if($repeatStartedDeu == 0) {
                if($todayStartedDeu == 1){
                    $this->deuRepo->breakTodayWords();
                    showMessage('Poprawnie zresetowano codzienną powtórkę!');
                }
                else{
                    showError('Codzienna powtórka nie jest uruchomiona!');
                }
            }
            else{
                showError('Nie możesz zresetować dzisiejszej powtórki, ponieważ inna powtórka jest uruchomiona!');
            }
        }

    }


}