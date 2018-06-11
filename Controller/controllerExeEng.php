<?php
require_once './Model/exeEngNameRepo.php';
require_once './Model/exeEngRepo.php';
require_once './Model/configRepo.php';

class controllerExeEng
{
    private $exeEngNameRepo;
    private $exeEngRepo;
    private $configRepo;

    public function __construct()
    {
        $this->exeEngNameRepo = new exeEngNameRepo();
        $this->exeEngRepo = new exeEngRepo();
        $this->configRepo = new configRepo();

        $countWordsToEnd = $this->exeEngRepo->countWordsToEnd();

        if (isset($_GET['today'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeEng');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeEng');

            if (($repeatStarted == 0) && ($todayStarted == 0)) {
                header("Location: index.php?exeEng");
            }

            $showFirstBase = 0;
            $showWord = "";
            $tab = NULL;
            if (isset($_POST['sendAnswer'])) {
                $showFirstBase = 1;

                if (empty($_POST['answer'])) {
                    $_POST['answer'] = "0";
                }

                $checkAnswerToday = $this->exeEngRepo->checkAnswerToday($_POST['id'], $_POST['wordSide'], addslashes($_POST['answer']));

                if ($checkAnswerToday == 1) {
                    $tab['color'] = "#009900";
                } else {
                    $tab['color'] = "#990000";
                }
                $exe = $this->exeEngRepo->getExeOne($_POST['id']);
                $tab['id'] = $exe->getId();

                $tab['wordSide'] = $_POST['wordSide'];

                $tab['comment'] = $_POST['comment'];

                if ($_POST['wordSide'] == 1) {
                    $tab['question'] = $exe->getFirst();
                    $tab['answer'] = $exe->getSecond();
                } else {
                    $tab['question'] = $exe->getSecond();
                    $tab['answer'] = $exe->getFirst();
                }

                $this->exeEngRepo->saveAnswerToday($_POST['id'], $checkAnswerToday, $_POST['wordSide'], $_POST['working']);
                $countWordsToEnd = $this->exeEngRepo->countWordsToEnd();
            } else {
                $showFirstBase = 0;
                $showWord = $this->exeEngRepo->showTodayWord();
            }


            require_once './view/exeEngToday.html';
            return 0;
        }


        $this->controller();

        $repeatStartedExeEng = $this->configRepo->getConfig('repeatStartedExeEng');
        $todayStartedExeEng = $this->configRepo->getConfig('todayStartedExeEng');

        $showExeEngName = $this->exeEngNameRepo->getExeEngName();

        if (isset($_GET['show'])) {
            $showExeEng = $this->exeEngRepo->getExeEngSort();
            $showExeEng = $this->exeEngRepo->prepareToShowExeEngSort($showExeEng);
        }

        $getCountToEndAll = $this->exeEngRepo->getCountToEndAll();

        $tabOfExeNameToJS = array();

        require_once './view/exeEng.html';
    }

    private function controller()
    {
        if (isset($_POST['editName'])) {
            $this->exeEngNameRepo->editName($_POST['editNameId'], $_POST['editName']);
        }
        if (isset($_POST['addNewExeName'])) {
            $this->exeEngNameRepo->addNewExeName($_POST['addNewExeName']);
        }
        if (isset($_POST['addNewExe'])) {
            if ((!empty($_POST['first'])) AND (!empty($_POST['second'])) AND ($_POST['working'] != 0) AND ($_POST['mode'] != 0)) {
                $this->exeEngRepo->addNewExe($_POST['idExeName'], $_POST['first'], $_POST['second'],
                    $_POST['word'], $_POST['comment'], $_POST['working'], $_POST['mode']);
            } else {
                showError("First i second nie mogą być puste oraz working i mode muszą być wybrane!!!");
            }
        }
        if (isset($_POST['editFirst'])) {
            $this->exeEngRepo->editFirst($_POST['editFirstId'], $_POST['editFirst']);
        }
        if (isset($_POST['editSecond'])) {
            $this->exeEngRepo->editSecond($_POST['editSecondId'], $_POST['editSecond']);
        }
        if (isset($_POST['editWord'])) {
            $this->exeEngRepo->editWord($_POST['editWordId'], $_POST['editWord']);
        }
        if (isset($_POST['editComment'])) {
            $this->exeEngRepo->editComment($_POST['editCommentId'], $_POST['editComment']);
        }
        if (isset($_POST['editWorking'])) {
            $this->exeEngRepo->editWorking($_POST['editWorkingId'], $_POST['editWorking']);
        }
        if (isset($_POST['editMode'])) {
            $this->exeEngRepo->editMode($_POST['editModeId'], $_POST['editMode']);
        }
        if (isset($_POST['editExeName'])) {
            $this->exeEngRepo->editExeName($_POST['editExeNameId'], $_POST['editExeName']);
        }
        if (isset($_POST['breakRepeatWordsExeEng'])) {
            $this->exeEngRepo->breakRepeatWords();
        }
        if (isset($_POST['resetAllWordsExeEng'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeEng');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeEng');
            if (($repeatStarted == 0) && ($todayStarted == 0)) {
                $this->exeEngRepo->resetAllWords();
                showMessage('Zresetowano wszystkie ćwiczenia! Możesz już je powtarzać.');
            } else {
                showError('Nie możesz resetować ćwiczeń, gdy uruchomiona jest jakaś powtórka!');
            }
        }

        if (isset($_POST['resetWordsWithRangeFrom'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeEng');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeEng');
            if (($repeatStarted == 0) && ($todayStarted == 0)) {
                $this->exeEngRepo->resetWordsWithRange($_POST['resetWordsWithRangeFrom'], $_POST['resetWordsWithRangeTo']);
            } else {
                showError('Nie możesz resetować ćwiczeń, gdy uruchomiona jest jakaś powtórka!');
            }
        }

        if (isset($_POST['resetTodayWordsExeEng'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeEng');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeEng');
            if ($repeatStarted == 0) {
                if ($todayStarted == 1) {
                    $this->exeEngRepo->breakTodayWords();
                    showMessage('Poprawnie zresetowano codzienną powtórkę!');
                } else {
                    showError('Codzienna powtórka nie jest uruchomiona!');
                }
            } else {
                showError('Nie możesz zresetować dzisiejszej powtórki, ponieważ inna powtórka jest uruchomiona!');
            }
        }

        if (isset($_POST['startRepeatWordsFrom'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeEng');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeEng');
            if (($repeatStarted == 0) && ($todayStarted == 0)) {
                if (empty($_POST['startRepeatWordsFrom']) OR empty($_POST['countEachExe'])) {
                    showError('Musisz podać wartość choć w pierwszym polu oraz ilość!');
                } else {
                    $this->exeEngRepo->setRepeatWords($_POST['startRepeatWordsFrom'], $_POST['startRepeatWordsTo'], $_POST['countEachExe']);
                    header("Location: index.php?exeEng&today");
                }
            } else {
                showError('Nie możesz uruchomić powtórki, jeżeli jest już jakaś uruchomiona!');
            }
        }

        if (isset($_POST['startTodayExeEng'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeEng');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeEng');
            if ($repeatStarted == 0) {
                if ($todayStarted == 0) {
                    $this->exeEngRepo->setTodayWords($_POST['startTodayExeEng']);
                }
                header("Location: index.php?exeEng&today");
            } else {
                showError('Nie możesz uruchomić powtórki, jeżeli jest już uruchomina inna!');
            }
        }
    }
}