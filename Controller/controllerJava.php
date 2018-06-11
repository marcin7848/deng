<?php

require_once './Model/javaRepo.php';
require_once './Model/configRepo.php';

class controllerJava
{
    private $javaRepo;
    private $configRepo;

    public function __construct()
    {
        $this->javaRepo = new javaRepo();
        $this->configRepo = new configRepo();

        $countWordsToEndJava = $this->javaRepo->countWordsToEnd();

        if(isset($_GET['today'])){
            $repeatStartedJava = $this->configRepo->getConfig('repeatStartedJava');
            $todayStartedJava = $this->configRepo->getConfig('todayStartedJava');

            if(($repeatStartedJava == 0) && ($todayStartedJava == 0))
            {
                header("Location: index.php?java");
            }

            $showFirstBase = 0;
            $showWord = "";
            $tab = NULL;
            if(isset($_POST['sendAnswer'])){
                $showFirstBase = 1;

                if(empty($_POST['answer'])){
                    $_POST['answer'] = "0";
                }

                $checkAnswerToday = $this->javaRepo->checkAnswerToday($_POST['id'], $_POST['wordSide'], addslashes($_POST['answer']));

                if($checkAnswerToday == 1){
                    $tab['color'] = "#009900";
                }
                else{
                    $tab['color'] = "#990000";
                }
                $java = $this->javaRepo->getNoteOne($_POST['id']);
                $tab['id'] = $java->getId();

                $tab['wordSide'] = $_POST['wordSide'];

                if($_POST['wordSide'] == 1){
                    $tab['word'] = $java->getPolish();
                    $tab['answer'] = $java->getFunction();
                }
                else{
                    $tab['word'] = $java->getFunction();
                    $tab['answer'] = $java->getPolish();
                }

                $this->javaRepo->saveAnswerToday($_POST['id'], $checkAnswerToday, $_POST['wordSide']);
                $countWordsToEndJava = $this->javaRepo->countWordsToEnd();

            }
            else{
                $showFirstBase = 0;
                $showWord = $this->javaRepo->showTodayWord();
            }



            require_once './view/javaToday.html';
            return 0;
        }

        $this->controller();

        if(isset($_GET['show'])){
            $showJava = $this->javaRepo->getJava();
        }

        $todaysJavaWords = $this->configRepo->getConfig('todaysJavaWords');
        $thisUnitJava = $this->configRepo->getConfig('thisUnitJava');
        $workingJava = $this->configRepo->getConfig('workingJava');
        $modeJava = $this->configRepo->getConfig('modeJava');
        $repeatStartedJava = $this->configRepo->getConfig('repeatStartedJava');
        $todayStartedJava = $this->configRepo->getConfig('todayStartedJava');



        $getCountToEndAll = $this->javaRepo->getCountToEndAll();

        require_once './view/java.html';

    }

    private function controller(){
        if(isset($_GET['changeThisUnit'])){
            $this->javaRepo->changeThisUnit($_GET['changeThisUnit']);
        }

        if(isset($_POST['editFirstWord'])){
            $this->javaRepo->editFunction($_POST['editFirstWord'], $_POST['editValueFirstWord']);
        }

        if(isset($_POST['editSecondWord'])){
            $this->javaRepo->editPolish($_POST['editSecondWord'], $_POST['editValueSecondWord']);
        }

        if(isset($_POST['addNewJava'])){
            if(empty($_POST['addNewJava']) || empty($_POST['addNewPl'])){
                showError('Uzupełnij oba pola przy dodawaniu funkcji!');
            }
            else{
                $this->javaRepo->addNewWord($_POST['addNewPl'], $_POST['addNewJava']);
            }
        }

        if(isset($_POST['todaysJavaWords'])){
            $this->configRepo->setConfig('todaysJavaWords', $_POST['todaysJavaWords']);

        }

        if(isset($_POST['changeThisUnitJava'])){
            $thisUnitJava = $this->configRepo->getConfig('thisUnitJava');
            $newThisUnitJava = ($thisUnitJava == 1) ? 0 : 1;
            $this->configRepo->setConfig('thisUnitJava', $newThisUnitJava);
        }

        if(isset($_POST['deleteThisUnit'])){
            $this->javaRepo->deleteThisUnit();
            showMessage('Usunąłeś This Unit ze wszystkich funkcji!');
        }

        if(isset($_POST['changeWorkingJava'])){
            $workingJava = $this->configRepo->getConfig('workingJava');
            if($workingJava < 3){
                $workingJava++;
            }
            else{
                $workingJava = 1;
            }

            $this->configRepo->setConfig('workingJava', $workingJava);

            showMessage('Poprawnie zmieniono działanie java!');
        }
        if(isset($_POST['changeModeJava'])){
            $modeJava = $this->configRepo->getConfig('modeJava');
            if($modeJava < 3){
                $modeJava++;
            }
            else{
                $modeJava = 1;
            }

            $this->configRepo->setConfig('modeJava', $modeJava);

            showMessage('Poprawnie zmieniono tryb java!');
        }

        if(isset($_POST['resetAllWordsJava'])){
            $repeatStartedJava = $this->configRepo->getConfig('repeatStartedJava');
            $todayStartedJava = $this->configRepo->getConfig('todayStartedJava');
            if(($repeatStartedJava == 0) && ($todayStartedJava == 0)) {
                $this->javaRepo->resetAllWords();
                showMessage('Zresetowano wszystkie funkcje! Możesz już je powtarzać.');
            }
            else{
                showError('Nie możesz resetować funkcji, gdy uruchomiona jest jakaś powtórka!');
            }
        }

        if(isset($_POST['resetWordsWithRangeFrom'])){
            $repeatStartedJava = $this->configRepo->getConfig('repeatStartedJava');
            $todayStartedJava = $this->configRepo->getConfig('todayStartedJava');
            if(($repeatStartedJava == 0) && ($todayStartedJava == 0)) {
                $this->javaRepo->resetWordsWithRange($_POST['resetWordsWithRangeFrom'], $_POST['resetWordsWithRangeTo']);
                showMessage('Zresetowano funkcje z zakresu: ' . $_POST["resetWordsWithRangeFrom"] . '-' . $_POST["resetWordsWithRangeTo"] . '');
            }
            else{
                showError('Nie możesz resetować funkcji, gdy uruchomiona jest jakaś powtórka!');
            }
        }


        if(isset($_POST['breakRepeatWordsJava'])){
            $this->javaRepo->breakRepeatWords();
            showMessage('Przerwałeś powtórkę funkcji!');
        }

        if(isset($_POST['startRepeatWordsFrom'])){
            $repeatStartedJava = $this->configRepo->getConfig('repeatStartedJava');
            $todayStartedJava = $this->configRepo->getConfig('todayStartedJava');
            if(($repeatStartedJava == 0) && ($todayStartedJava == 0)) {
                if (empty($_POST['startRepeatWordsFrom']) OR empty($_POST['startRepeatWordsTo'])) {
                    showError('Musisz podać oba zakresy!');
                } else {
                    $this->javaRepo->setRepeatWords($_POST['startRepeatWordsFrom'], $_POST['startRepeatWordsTo']);
                    header("Location: index.php?java&today");
                }
            }
            else{
                showError('Nie możesz uruchomić powtórki, jeżeli jest już jakaś uruchomiona!');
            }
        }

        if(isset($_POST['startTodayJava'])){
            $repeatStartedJava = $this->configRepo->getConfig('repeatStartedJava');
            $todayStartedJava = $this->configRepo->getConfig('todayStartedJava');
            if($repeatStartedJava == 0){
                if($todayStartedJava == 0) {
                    $this->javaRepo->setTodayWords();
                }
                header("Location: index.php?java&today");
            }
            else{
                showError('Nie możesz uruchomić powtórki, jeżeli jest już uruchomina inna!');
            }
        }

        if(isset($_POST['resetTodayWordsJava'])){
            $repeatStartedJava = $this->configRepo->getConfig('repeatStartedJava');
            $todayStartedJava = $this->configRepo->getConfig('todayStartedJava');
            if($repeatStartedJava == 0) {
                if($todayStartedJava == 1){
                    $this->javaRepo->breakTodayWords();
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