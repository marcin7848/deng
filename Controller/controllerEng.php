<?php

require_once './Model/engRepo.php';
require_once './Model/configRepo.php';

class controllerEng
{
    private $engRepo;
    private $configRepo;

    public function __construct()
    {
        $this->engRepo = new engRepo();
        $this->configRepo = new configRepo();

        $countWordsToEndEng = $this->engRepo->countWordsToEnd();

        if(isset($_GET['today'])){
            $repeatStartedEng = $this->configRepo->getConfig('repeatStartedEng');
            $todayStartedEng = $this->configRepo->getConfig('todayStartedEng');

            if(($repeatStartedEng == 0) && ($todayStartedEng == 0))
            {
                header("Location: index.php?eng");
            }

            $showFirstBase = 0;
            $showWord = "";
            $tab = NULL;
            if(isset($_POST['sendAnswer'])){
                $showFirstBase = 1;

                if(empty($_POST['answer'])){
                    $_POST['answer'] = "0";
                }

                $checkAnswerToday = $this->engRepo->checkAnswerToday($_POST['id'], $_POST['wordSide'], addslashes($_POST['answer']));

                if($checkAnswerToday == 1){
                    $tab['color'] = "#009900";
                }
                else{
                    $tab['color'] = "#990000";
                }
                $eng = $this->engRepo->getNoteOne($_POST['id']);
                $tab['id'] = $eng->getId();

                $tab['wordSide'] = $_POST['wordSide'];

                if($_POST['wordSide'] == 1){
                    $tab['word'] = $eng->getPolish();
                    $tab['answer'] = $eng->getEnglish();
                }
                else{
                    $tab['word'] = $eng->getEnglish();
                    $tab['answer'] = $eng->getPolish();
                }

                $this->engRepo->saveAnswerToday($_POST['id'], $checkAnswerToday, $_POST['wordSide']);
                $countWordsToEndEng = $this->engRepo->countWordsToEnd();

            }
            else{
                $showFirstBase = 0;
                $showWord = $this->engRepo->showTodayWord();
            }



            require_once './view/engToday.html';
            return 0;
        }

        $this->controller();

        if(isset($_GET['show'])){
            $showEng = $this->engRepo->getEng();
        }

        $todaysEngWords = $this->configRepo->getConfig('todaysEngWords');
        $thisUnitEng = $this->configRepo->getConfig('thisUnitEng');
        $workingEng = $this->configRepo->getConfig('workingEng');
        $modeEng = $this->configRepo->getConfig('modeEng');
        $repeatStartedEng = $this->configRepo->getConfig('repeatStartedEng');
        $todayStartedEng = $this->configRepo->getConfig('todayStartedEng');



        $getCountToEndAll = $this->engRepo->getCountToEndAll();

        require_once './view/eng.html';

    }

    private function controller(){
        if(isset($_GET['changeThisUnit'])){
            $this->engRepo->changeThisUnit($_GET['changeThisUnit']);
        }

        if(isset($_POST['editFirstWord'])){
            $this->engRepo->editEnglish($_POST['editFirstWord'], $_POST['editValueFirstWord']);
        }

        if(isset($_POST['editSecondWord'])){
            $this->engRepo->editPolish($_POST['editSecondWord'], $_POST['editValueSecondWord']);
        }

        if(isset($_POST['addNewEng'])){
            if(empty($_POST['addNewEng']) || empty($_POST['addNewPl'])){
                showError('Uzupełnij oba pola przy dodawaniu słowa!');
            }
            else{
                $this->engRepo->addNewWord($_POST['addNewPl'], $_POST['addNewEng']);
            }
        }

        if(isset($_POST['todaysEngWords'])){
            $this->configRepo->setConfig('todaysEngWords', $_POST['todaysEngWords']);

        }

        if(isset($_POST['changeThisUnitEng'])){
            $thisUnitEng = $this->configRepo->getConfig('thisUnitEng');
            $newThisUnitEng = ($thisUnitEng == 1) ? 0 : 1;
            $this->configRepo->setConfig('thisUnitEng', $newThisUnitEng);
        }

        if(isset($_POST['deleteThisUnit'])){
            $this->engRepo->deleteThisUnit();
            showMessage('Usunąłeś This Unit ze wszystkich słów!');
        }

        if(isset($_POST['changeWorkingEng'])){
            $workingEng = $this->configRepo->getConfig('workingEng');
            if($workingEng < 3){
                $workingEng++;
            }
            else{
                $workingEng = 1;
            }

            $this->configRepo->setConfig('workingEng', $workingEng);

            showMessage('Poprawnie zmieniono działanie eng!');
        }
        if(isset($_POST['changeModeEng'])){
            $modeEng = $this->configRepo->getConfig('modeEng');
            if($modeEng < 3){
                $modeEng++;
            }
            else{
                $modeEng = 1;
            }

            $this->configRepo->setConfig('modeEng', $modeEng);

            showMessage('Poprawnie zmieniono tryb eng!');
        }

        if(isset($_POST['resetAllWordsEng'])){
            $repeatStartedEng = $this->configRepo->getConfig('repeatStartedEng');
            $todayStartedEng = $this->configRepo->getConfig('todayStartedEng');
            if(($repeatStartedEng == 0) && ($todayStartedEng == 0)) {
                $this->engRepo->resetAllWords();
                showMessage('Zresetowano wszystkie słowa! Możesz już je powtarzać.');
            }
            else{
                showError('Nie możesz resetować słów, gdy uruchomiona jest jakaś powtórka!');
            }
        }

        if(isset($_POST['resetWordsWithRangeFrom'])){
            $repeatStartedEng = $this->configRepo->getConfig('repeatStartedEng');
            $todayStartedEng = $this->configRepo->getConfig('todayStartedEng');
            if(($repeatStartedEng == 0) && ($todayStartedEng == 0)) {
                $this->engRepo->resetWordsWithRange($_POST['resetWordsWithRangeFrom'], $_POST['resetWordsWithRangeTo']);
                showMessage('Zresetowano słowa z zakresu: ' . $_POST["resetWordsWithRangeFrom"] . '-' . $_POST["resetWordsWithRangeTo"] . '');
            }
            else{
                showError('Nie możesz resetować słów, gdy uruchomiona jest jakaś powtórka!');
            }
        }


        if(isset($_POST['breakRepeatWordsEng'])){
            $this->engRepo->breakRepeatWords();
            showMessage('Przerwałeś powtórkę słów!');
        }

        if(isset($_POST['startRepeatWordsFrom'])){
            $repeatStartedEng = $this->configRepo->getConfig('repeatStartedEng');
            $todayStartedEng = $this->configRepo->getConfig('todayStartedEng');
            if(($repeatStartedEng == 0) && ($todayStartedEng == 0)) {
                if (empty($_POST['startRepeatWordsFrom']) OR empty($_POST['startRepeatWordsTo'])) {
                    showError('Musisz podać oba zakresy!');
                } else {
                    $this->engRepo->setRepeatWords($_POST['startRepeatWordsFrom'], $_POST['startRepeatWordsTo']);
                    header("Location: index.php?eng&today");
                }
            }
            else{
                showError('Nie możesz uruchomić powtórki, jeżeli jest już jakaś uruchomiona!');
            }
        }

        if(isset($_POST['startTodayEng'])){
            $repeatStartedEng = $this->configRepo->getConfig('repeatStartedEng');
            $todayStartedEng = $this->configRepo->getConfig('todayStartedEng');
            if($repeatStartedEng == 0){
                if($todayStartedEng == 0) {
                    $this->engRepo->setTodayWords();
                }
                header("Location: index.php?eng&today");
            }
            else{
                showError('Nie możesz uruchomić powtórki, jeżeli jest już uruchomina inna!');
            }
        }

        if(isset($_POST['resetTodayWordsEng'])){
            $repeatStartedEng = $this->configRepo->getConfig('repeatStartedEng');
            $todayStartedEng = $this->configRepo->getConfig('todayStartedEng');
            if($repeatStartedEng == 0) {
                if($todayStartedEng == 1){
                    $this->engRepo->breakTodayWords();
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