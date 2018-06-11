<?php

function showError($message)
{
    echo '<script src="./view/cssJS/jquery-3.2.1.js"></script>',
    '<script type="text/javascript">',
    '$(function(){',
        '$("body").prepend("<div id=\"error\" style=\"position: absolute; background-color: red; left: 0; right:0; height: 50px;text-align: center;font-size: 20px;\">' . $message . '</div>");',
    'setTimeout(function(){$("#error").remove();}, 2000);',
    '});</script>';
}

function showMessage($message)
{
    echo '<script src="./view/cssJS/jquery-3.2.1.js"></script>',
    '<script type="text/javascript">',
    '$(function(){',
        '$("body").prepend("<div id=\"message\" style=\"position: absolute; background-color: green; left: 0; right:0; height: 50px;text-align: center;font-size: 20px;\">' . $message . '</div>");',
    'setTimeout(function(){$("#message").remove();}, 2000);',
    '});</script>';
}

function sendFromGirls($tableName, $exeName, $first, $second, $word, $comment, $working, $mode)
{
    global $fromgirlsHost;
    $ch = curl_init("http://" . $fromgirlsHost . "/fromgirls.php?getFromGirls");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $postVars = array(
        'tableName' => $tableName,
        'exeName' => $exeName,
        'first' => $first,
        'second' => $second,
        'word' => $word,
        'comment' => $comment,
        'working' => $working,
        'mode' => $mode
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);

    $zrodlo = curl_exec($ch);
    curl_close($ch);

    global $fromgirlsHost2;
    $ch = curl_init("http://" . $fromgirlsHost2 . "/fromgirls.php?getFromGirls");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $postVars = array(
        'tableName' => $tableName,
        'exeName' => $exeName,
        'first' => $first,
        'second' => $second,
        'word' => $word,
        'comment' => $comment,
        'working' => $working,
        'mode' => $mode
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);

    $zrodlo = curl_exec($ch);
    curl_close($ch);
}

function checkNoteJustification($db)
{
    $query = "SELECT COUNT(*) AS count FROM note WHERE done<>1 AND date < CURDATE();";
    $query = $db->getQuery($query);
    $result = $query->fetch(PDO::FETCH_BOTH);
    $count = $result['count'];
    if ($count > 0) {
        return 0;
    }
    return 1;
}

require_once 'controllerNote.php';
require_once 'controllerEng.php';
require_once 'controllerDeu.php';
require_once 'controllerArchive.php';
require_once 'controllerExeEng.php';
require_once 'controllerExeDeu.php';
require_once 'controllerExeJava.php';
require_once 'controllerIrVerbsEng.php';
require_once 'controllerIrVerbsDeu.php';

require_once './db_connect.php';
require_once './Model/worksRepo.php';
require_once './Model/noteRepo.php';

class controller
{
    public function __construct()
    {
        $worksRepo = new worksRepo();
        $worksRepo->checkWorksDate();
        $noteRepo = new noteRepo();
        $noteRepo->setDoneNotes();

        $db = new db_connect();

        if (isset($_GET['eng'])) {
            if (checkNoteJustification($db) == 1) {
                new controllerEng();
            } else {
                new controllerNote();
                showError("Musisz usprawiedliwić wszystkie dni, aby móc wejść dalej!");
            }
        } else if (isset($_GET['deu'])) {
            if (checkNoteJustification($db) == 1) {
                new controllerDeu();
            } else {
                new controllerNote();
                showError("Musisz usprawiedliwić wszystkie dni, aby móc wejść dalej!");
            }
        } else if (isset($_GET['exeJava'])) {
            if (checkNoteJustification($db) == 1) {
                new controllerExeJava();
            } else {
                new controllerNote();
                showError("Musisz usprawiedliwić wszystkie dni, aby móc wejść dalej!");
            }
        } else if (isset($_GET['archive'])) {
            new controllerArchive();
        } else if (isset($_GET['exeEng'])) {
            if (checkNoteJustification($db) == 1) {
                new controllerExeEng();
            } else {
                new controllerNote();
                showError("Musisz usprawiedliwić wszystkie dni, aby móc wejść dalej!");
            }
        } else if (isset($_GET['exeDeu'])) {
            if (checkNoteJustification($db) == 1) {
                new controllerExeDeu();
            } else {
                new controllerNote();
                showError("Musisz usprawiedliwić wszystkie dni, aby móc wejść dalej!");
            }
        } else if (isset($_GET['irVerbsEng'])) {
            if (checkNoteJustification($db) == 1) {
                new controllerIrVerbsEng();
            } else {
                new controllerNote();
                showError("Musisz usprawiedliwić wszystkie dni, aby móc wejść dalej!");
            }

        } else if (isset($_GET['irVerbsDeu'])) {
            if (checkNoteJustification($db) == 1) {
                new controllerIrVerbsDeu();
            } else {
                new controllerNote();
                showError("Musisz usprawiedliwić wszystkie dni, aby móc wejść dalej!");
            }
        } else {
            new controllerNote();
        }
    }
}
