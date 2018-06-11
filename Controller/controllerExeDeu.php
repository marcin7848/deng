<?php
require_once './Model/exeDeuNameRepo.php';
require_once './Model/exeDeuRepo.php';
require_once './Model/configRepo.php';

class controllerExeDeu
{
    private $exeDeuNameRepo;
    private $exeDeuRepo;
    private $configRepo;

    public function __construct()
    {
        $this->exeDeuNameRepo = new exeDeuNameRepo();
        $this->exeDeuRepo = new exeDeuRepo();
        $this->configRepo = new configRepo();

        $countWordsToEnd = $this->exeDeuRepo->countWordsToEnd();

        if (isset($_GET['today'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeDeu');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeDeu');

            if (($repeatStarted == 0) && ($todayStarted == 0)) {
                header("Location: index.php?exeDeu");
            }

            $showFirstBase = 0;
            $showWord = "";
            $tab = NULL;
            if (isset($_POST['sendAnswer'])) {
                $showFirstBase = 1;

                if (empty($_POST['answer'])) {
                    $_POST['answer'] = "0";
                }

                $checkAnswerToday = $this->exeDeuRepo->checkAnswerToday($_POST['id'], $_POST['wordSide'], addslashes($_POST['answer']));

                if ($checkAnswerToday == 1) {
                    $tab['color'] = "#009900";
                } else {
                    $tab['color'] = "#990000";
                }
                $exe = $this->exeDeuRepo->getExeOne($_POST['id']);
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

                $this->exeDeuRepo->saveAnswerToday($_POST['id'], $checkAnswerToday, $_POST['wordSide'], $_POST['working']);
                $countWordsToEnd = $this->exeDeuRepo->countWordsToEnd();
            } else {
                $showFirstBase = 0;
                $showWord = $this->exeDeuRepo->showTodayWord();
            }


            require_once './view/exeDeuToday.html';
            return 0;
        }


        $this->controller();

        $repeatStartedExeDeu = $this->configRepo->getConfig('repeatStartedExeDeu');
        $todayStartedExeDeu = $this->configRepo->getConfig('todayStartedExeDeu');

        $showExeDeuName = $this->exeDeuNameRepo->getExeDeuName();

        if (isset($_GET['show'])) {
            $showExeDeu = $this->exeDeuRepo->getExeDeuSort();
            $showExeDeu = $this->exeDeuRepo->prepareToShowExeDeuSort($showExeDeu);
        }

        $getCountToEndAll = $this->exeDeuRepo->getCountToEndAll();

        $tabOfExeNameToJS = array();

        require_once './view/exeDeu.html';
    }

    private function controller()
    {
        if (isset($_POST['editName'])) {
            $this->exeDeuNameRepo->editName($_POST['editNameId'], $_POST['editName']);
        }
        if (isset($_POST['addNewExeName'])) {
            $this->exeDeuNameRepo->addNewExeName($_POST['addNewExeName']);
        }
        if (isset($_POST['addNewExe'])) {
            if ((!empty($_POST['first'])) AND (!empty($_POST['second'])) AND ($_POST['working'] != 0) AND ($_POST['mode'] != 0)) {
                $this->exeDeuRepo->addNewExe($_POST['idExeName'], $_POST['first'], $_POST['second'],
                    $_POST['word'], $_POST['comment'], $_POST['working'], $_POST['mode']);
            } else {
                showError("First i second nie mogą być puste oraz working i mode muszą być wybrane!!!");
            }
        }
        if (isset($_POST['editFirst'])) {
            $this->exeDeuRepo->editFirst($_POST['editFirstId'], $_POST['editFirst']);
        }
        if (isset($_POST['editSecond'])) {
            $this->exeDeuRepo->editSecond($_POST['editSecondId'], $_POST['editSecond']);
        }
        if (isset($_POST['editWord'])) {
            $this->exeDeuRepo->editWord($_POST['editWordId'], $_POST['editWord']);
        }
        if (isset($_POST['editComment'])) {
            $this->exeDeuRepo->editComment($_POST['editCommentId'], $_POST['editComment']);
        }
        if (isset($_POST['editWorking'])) {
            $this->exeDeuRepo->editWorking($_POST['editWorkingId'], $_POST['editWorking']);
        }
        if (isset($_POST['editMode'])) {
            $this->exeDeuRepo->editMode($_POST['editModeId'], $_POST['editMode']);
        }
        if (isset($_POST['editExeName'])) {
            $this->exeDeuRepo->editExeName($_POST['editExeNameId'], $_POST['editExeName']);
        }
        if (isset($_POST['breakRepeatWordsExeDeu'])) {
            $this->exeDeuRepo->breakRepeatWords();
        }
        if (isset($_POST['resetAllWordsExeDeu'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeDeu');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeDeu');
            if (($repeatStarted == 0) && ($todayStarted == 0)) {
                $this->exeDeuRepo->resetAllWords();
                showMessage('Zresetowano wszystkie ćwiczenia! Możesz już je powtarzać.');
            } else {
                showError('Nie możesz resetować ćwiczeń, gdy uruchomiona jest jakaś powtórka!');
            }
        }

        if (isset($_POST['resetWordsWithRangeFrom'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeDeu');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeDeu');
            if (($repeatStarted == 0) && ($todayStarted == 0)) {
                $this->exeDeuRepo->resetWordsWithRange($_POST['resetWordsWithRangeFrom'], $_POST['resetWordsWithRangeTo']);
            } else {
                showError('Nie możesz resetować ćwiczeń, gdy uruchomiona jest jakaś powtórka!');
            }
        }

        if (isset($_POST['resetTodayWordsExeDeu'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeDeu');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeDeu');
            if ($repeatStarted == 0) {
                if ($todayStarted == 1) {
                    $this->exeDeuRepo->breakTodayWords();
                    showMessage('Poprawnie zresetowano codzienną powtórkę!');
                } else {
                    showError('Codzienna powtórka nie jest uruchomiona!');
                }
            } else {
                showError('Nie możesz zresetować dzisiejszej powtórki, ponieważ inna powtórka jest uruchomiona!');
            }
        }

        if (isset($_POST['startRepeatWordsFrom'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeDeu');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeDeu');
            if (($repeatStarted == 0) && ($todayStarted == 0)) {
                if (empty($_POST['startRepeatWordsFrom']) OR empty($_POST['countEachExe'])) {
                    showError('Musisz podać wartość choć w pierwszym polu oraz ilość!');
                } else {
                    $this->exeDeuRepo->setRepeatWords($_POST['startRepeatWordsFrom'], $_POST['startRepeatWordsTo'], $_POST['countEachExe']);
                    header("Location: index.php?exeDeu&today");
                }
            } else {
                showError('Nie możesz uruchomić powtórki, jeżeli jest już jakaś uruchomiona!');
            }
        }

        if (isset($_POST['startTodayExeDeu'])) {
            $repeatStarted = $this->configRepo->getConfig('repeatStartedExeDeu');
            $todayStarted = $this->configRepo->getConfig('todayStartedExeDeu');
            if ($repeatStarted == 0) {
                if ($todayStarted == 0) {
                    $this->exeDeuRepo->setTodayWords($_POST['startTodayExeDeu']);
                }
                header("Location: index.php?exeDeu&today");
            } else {
                showError('Nie możesz uruchomić powtórki, jeżeli jest już uruchomina inna!');
            }
        }
    }
}