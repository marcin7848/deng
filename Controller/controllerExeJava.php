<?php
require_once './Model/exeJavaNameRepo.php';
require_once './Model/exeJavaRepo.php';
require_once './Model/configRepo.php';

class controllerExeJava
{
    private $exeJavaNameRepo;
    private $exeJavaRepo;
    private $configRepo;

    public function __construct()
    {
        $this->exeJavaNameRepo = new exeJavaNameRepo();
        $this->exeJavaRepo = new exeJavaRepo();
        $this->configRepo = new configRepo();

        $countWordsToEnd = $this->exeJavaRepo->countWordsToEnd();

        if (isset($_GET['today'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeJava');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeJava');

            if (($repeatStarted == 0) && ($todayStarted == 0)) {
                header("Location: index.php?exeJava");
            }

            $showFirstBase = 0;
            $showWord = "";
            $tab = NULL;
            if (isset($_POST['sendAnswer'])) {
                $showFirstBase = 1;

                if (empty($_POST['answer'])) {
                    $_POST['answer'] = "0";
                }

                $checkAnswerToday = $this->exeJavaRepo->checkAnswerToday($_POST['id'], $_POST['wordSide'], addslashes($_POST['answer']));

                if ($checkAnswerToday == 1) {
                    $tab['color'] = "#009900";
                } else {
                    $tab['color'] = "#990000";
                }
                $exe = $this->exeJavaRepo->getExeOne($_POST['id']);
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

                $this->exeJavaRepo->saveAnswerToday($_POST['id'], $checkAnswerToday, $_POST['wordSide'], $_POST['working']);
                $countWordsToEnd = $this->exeJavaRepo->countWordsToEnd();
            } else {
                $showFirstBase = 0;
                $showWord = $this->exeJavaRepo->showTodayWord();
            }


            require_once './view/exeJavaToday.html';
            return 0;
        }


        $this->controller();

        $repeatStartedExeJava = $this->configRepo->getConfig('repeatStartedExeJava');
        $todayStartedExeJava = $this->configRepo->getConfig('todayStartedExeJava');

        $showExeJavaName = $this->exeJavaNameRepo->getExeJavaName();

        if (isset($_GET['show'])) {
            $showExeJava = $this->exeJavaRepo->getExeJavaSort();
            $showExeJava = $this->exeJavaRepo->prepareToShowExeJavaSort($showExeJava);
        }

        $getCountToEndAll = $this->exeJavaRepo->getCountToEndAll();

        $tabOfExeNameToJS = array();

        require_once './view/exeJava.html';
    }

    private function controller()
    {
        if (isset($_POST['editName'])) {
            $this->exeJavaNameRepo->editName($_POST['editNameId'], $_POST['editName']);
        }
        if (isset($_POST['addNewExeName'])) {
            $this->exeJavaNameRepo->addNewExeName($_POST['addNewExeName']);
        }
        if (isset($_POST['addNewExe'])) {
            if ((!empty($_POST['first'])) AND (!empty($_POST['second'])) AND ($_POST['working'] != 0) AND ($_POST['mode'] != 0)) {
                $this->exeJavaRepo->addNewExe($_POST['idExeName'], $_POST['first'], $_POST['second'],
                    $_POST['word'], $_POST['comment'], $_POST['working'], $_POST['mode']);
            } else {
                showError("First i second nie mogą być puste oraz working i mode muszą być wybrane!!!");
            }
        }
        if (isset($_POST['editFirst'])) {
            $this->exeJavaRepo->editFirst($_POST['editFirstId'], $_POST['editFirst']);
        }
        if (isset($_POST['editSecond'])) {
            $this->exeJavaRepo->editSecond($_POST['editSecondId'], $_POST['editSecond']);
        }
        if (isset($_POST['editWord'])) {
            $this->exeJavaRepo->editWord($_POST['editWordId'], $_POST['editWord']);
        }
        if (isset($_POST['editComment'])) {
            $this->exeJavaRepo->editComment($_POST['editCommentId'], $_POST['editComment']);
        }
        if (isset($_POST['editWorking'])) {
            $this->exeJavaRepo->editWorking($_POST['editWorkingId'], $_POST['editWorking']);
        }
        if (isset($_POST['editMode'])) {
            $this->exeJavaRepo->editMode($_POST['editModeId'], $_POST['editMode']);
        }
        if (isset($_POST['editExeName'])) {
            $this->exeJavaRepo->editExeName($_POST['editExeNameId'], $_POST['editExeName']);
        }
        if (isset($_POST['breakRepeatWordsExeJava'])) {
            $this->exeJavaRepo->breakRepeatWords();
        }
        if (isset($_POST['resetAllWordsExeJava'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeJava');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeJava');
            if (($repeatStarted == 0) && ($todayStarted == 0)) {
                $this->exeJavaRepo->resetAllWords();
                showMessage('Zresetowano wszystkie ćwiczenia! Możesz już je powtarzać.');
            } else {
                showError('Nie możesz resetować ćwiczeń, gdy uruchomiona jest jakaś powtórka!');
            }
        }

        if (isset($_POST['resetWordsWithRangeFrom'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeJava');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeJava');
            if (($repeatStarted == 0) && ($todayStarted == 0)) {
                $this->exeJavaRepo->resetWordsWithRange($_POST['resetWordsWithRangeFrom'], $_POST['resetWordsWithRangeTo']);
            } else {
                showError('Nie możesz resetować ćwiczeń, gdy uruchomiona jest jakaś powtórka!');
            }
        }

        if (isset($_POST['resetTodayWordsExeJava'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeJava');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeJava');
            if ($repeatStarted == 0) {
                if ($todayStarted == 1) {
                    $this->exeJavaRepo->breakTodayWords();
                    showMessage('Poprawnie zresetowano codzienną powtórkę!');
                } else {
                    showError('Codzienna powtórka nie jest uruchomiona!');
                }
            } else {
                showError('Nie możesz zresetować dzisiejszej powtórki, ponieważ inna powtórka jest uruchomiona!');
            }
        }

        if (isset($_POST['startRepeatWordsFrom'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeJava');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeJava');
            if (($repeatStarted == 0) && ($todayStarted == 0)) {
                if (empty($_POST['startRepeatWordsFrom']) OR empty($_POST['countEachExe'])) {
                    showError('Musisz podać wartość choć w pierwszym polu oraz ilość!');
                } else {
                    $this->exeJavaRepo->setRepeatWords($_POST['startRepeatWordsFrom'], $_POST['startRepeatWordsTo'], $_POST['countEachExe']);
                    header("Location: index.php?exeJava&today");
                }
            } else {
                showError('Nie możesz uruchomić powtórki, jeżeli jest już jakaś uruchomiona!');
            }
        }

        if (isset($_POST['startTodayExeJava'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeJava');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeJava');
            if ($repeatStarted == 0) {
                if ($todayStarted == 0) {
                    $this->exeJavaRepo->setTodayWords($_POST['startTodayExeJava']);
                }
                header("Location: index.php?exeJava&today");
            } else {
                showError('Nie możesz uruchomić powtórki, jeżeli jest już uruchomina inna!');
            }
        }
    }
}