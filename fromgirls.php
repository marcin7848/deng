<?php

header('Content-type: text/html; charset=UTF-8');
set_time_limit(0);

require_once 'db_connect.php';
require_once 'config.php';


$db = new db_connect();

if(!isset($_GET['getFromGirls'])) {

    require_once 'db_connect.php';

    echo '<link rel="stylesheet" href="./view/cssJS/layout.css">
    <script src="./view/cssJS/jquery-3.2.1.js"></script>
    <script src="./view/cssJS/script.js"></script>
    <script src="./view/cssJS/exeJS.js"></script>
    <style>
    body{
        background-image: none;
        color: #000;
        margin: 0 auto;
    }</style>';

    if (isset($_POST['editTableName'])) {
        $query = "UPDATE fromgirls SET tableName=:value WHERE id=" . $_POST['editTableNameId'];
        $stmt = $db->prepareQuery($query);
        $stmt->bindParam(':value', $_POST['editTableName']);
        $stmt->execute();
    }
    if (isset($_POST['editExeName'])) {
        $query = "UPDATE fromgirls SET exeName=:value WHERE id=" . $_POST['editExeNameId'];
        $stmt = $db->prepareQuery($query);
        $stmt->bindParam(':value', $_POST['editExeName']);
        $stmt->execute();
    }
    if (isset($_POST['editFirst'])) {
        $query = "UPDATE fromgirls SET first=:value WHERE id=" . $_POST['editFirstId'];
        $stmt = $db->prepareQuery($query);
        $stmt->bindParam(':value', $_POST['editFirst']);
        $stmt->execute();
    }
    if (isset($_POST['editSecond'])) {
        $query = "UPDATE fromgirls SET second=:value WHERE id=" . $_POST['editSecondId'];
        $stmt = $db->prepareQuery($query);
        $stmt->bindParam(':value', $_POST['editSecond']);
        $stmt->execute();
    }
    if (isset($_POST['editWord'])) {
        $query = "UPDATE fromgirls SET word=:value WHERE id=" . $_POST['editWordId'];
        $stmt = $db->prepareQuery($query);
        $stmt->bindParam(':value', $_POST['editWord']);
        $stmt->execute();
    }
    if (isset($_POST['editComment'])) {
        $query = "UPDATE fromgirls SET comment=:value WHERE id=" . $_POST['editCommentId'];
        $stmt = $db->prepareQuery($query);
        $stmt->bindParam(':value', $_POST['editComment']);
        $stmt->execute();
    }
    if (isset($_POST['editWorking'])) {
        $query = "UPDATE fromgirls SET working=:value WHERE id=" . $_POST['editWorkingId'];
        $stmt = $db->prepareQuery($query);
        $stmt->bindParam(':value', $_POST['editWorking']);
        $stmt->execute();
    }
    if (isset($_POST['editMode'])) {
        $query = "UPDATE fromgirls SET mode=:value WHERE id=" . $_POST['editModeId'];
        $stmt = $db->prepareQuery($query);
        $stmt->bindParam(':value', $_POST['editMode']);
        $stmt->execute();
    }
    if (isset($_GET['delete'])) {
        $query = "DELETE FROM fromgirls WHERE id=" . $_GET['delete'];
        $db->getQuery($query);
    }

    if (isset($_POST['add'])) {
        if ($_POST['tableName'] == 'exedeuname') {

            $query = "INSERT INTO exedeuname(name) VALUES(:newName);";
            $stmt = $db->prepareQuery($query);
            $stmt->bindParam(':newName', $_POST['exeName']);
            $stmt->execute();

            $query = "DELETE FROM fromgirls WHERE id=" . $_POST['id'];
            $db->getQuery($query);
        }
        if ($_POST['tableName'] == 'exeengname') {

            $query = "INSERT INTO exeengname(name) VALUES(:newName);";
            $stmt = $db->prepareQuery($query);
            $stmt->bindParam(':newName', $_POST['exeName']);
            $stmt->execute();

            $query = "DELETE FROM fromgirls WHERE id=" . $_POST['id'];
            $db->getQuery($query);
        }
        if ($_POST['tableName'] == 'exedeu') {
            $query = "SELECT * FROM exedeuname WHERE name='" . $_POST['exeName'] . "';";
            $query = $db->getQuery($query);
            $result = $query->fetch(PDO::FETCH_BOTH);
            $idExeName = $result['id'];

            $query = "INSERT INTO exedeu(idExeName, first, second, word, comment, working, mode, repeated, done, today, lastPerfectNum, lastPerfect) VALUES(:idExeName, :first, :second, :word, :comment, :working, :mode, 0,0,0,0,0);";
            $stmt = $db->prepareQuery($query);
            $stmt->bindParam(':idExeName', $idExeName);
            $stmt->bindParam(':first', $_POST['first']);
            $stmt->bindParam(':second', $_POST['second']);
            $stmt->bindParam(':word', $_POST['word']);
            $stmt->bindParam(':comment', $_POST['comment']);
            $stmt->bindParam(':working', $_POST['working']);
            $stmt->bindParam(':mode', $_POST['mode']);
            $stmt->execute();

            $query = "DELETE FROM fromgirls WHERE id=" . $_POST['id'];
            $db->getQuery($query);

        }
        if ($_POST['tableName'] == 'exeeng') {

            $query = "SELECT * FROM exeengname WHERE name='" . $_POST['exeName'] . "';";
            $query = $db->getQuery($query);
            $result = $query->fetch(PDO::FETCH_BOTH);
            $idExeName = $result['id'];

            $query = "INSERT INTO exeeng(idExeName, first, second, word, comment, working, mode, repeated, done, today, lastPerfectNum, lastPerfect) VALUES(:idExeName, :first, :second, :word, :comment, :working, :mode, 0,0,0,0,0);";
            $stmt = $db->prepareQuery($query);
            $stmt->bindParam(':idExeName', $idExeName);
            $stmt->bindParam(':first', $_POST['first']);
            $stmt->bindParam(':second', $_POST['second']);
            $stmt->bindParam(':word', $_POST['word']);
            $stmt->bindParam(':comment', $_POST['comment']);
            $stmt->bindParam(':working', $_POST['working']);
            $stmt->bindParam(':mode', $_POST['mode']);
            $stmt->execute();

            $query = "DELETE FROM fromgirls WHERE id=" . $_POST['id'];
            $db->getQuery($query);
        }

        if ($_POST['tableName'] == 'deu') {

            $query = "INSERT INTO deu(polish, deutsch, repeated, done, today, lastPerfectNum, lastPerfect, thisUnit) VALUES(:polish, :deutsch, 0, 0, 0, 0, 0, 0);";
            $stmt = $db->prepareQuery($query);
            $stmt->bindParam(':polish', $_POST['second']);
            $stmt->bindParam(':deutsch', $_POST['first']);
            $stmt->execute();

            $query = "DELETE FROM fromgirls WHERE id=" . $_POST['id'];
            $db->getQuery($query);
        }

        if ($_POST['tableName'] == 'eng') {
            $query = "INSERT INTO eng(polish, english, repeated, done, today, lastPerfectNum, lastPerfect, thisUnit) VALUES(:polish, :english, 0, 0, 0, 0, 0, 0);";
            $stmt = $db->prepareQuery($query);
            $stmt->bindParam(':polish', $_POST['second']);
            $stmt->bindParam(':english', $_POST['first']);
            $stmt->execute();

            $query = "DELETE FROM fromgirls WHERE id=" . $_POST['id'];
            $db->getQuery($query);
        }
    }


    $query = "SELECT * FROM fromgirls;";
    $query = $db->getQuery($query);
    echo '<table id="noteTable">';
    echo '<tr><td colspan="11">Working: 1-First->Second, 2-Second->First, 3-W obie strony;;;; Mode: 1-Zwykłe wpisanie, 2-Wybór z listy</td></tr>';
    echo '<tr>';
    echo '<td>ID</td>';
    echo '<td>TableName</td>';
    echo '<td>ExeName</td>';
    echo '<td>First</td>';
    echo '<td>Second</td>';
    echo '<td>Word</td>';
    echo '<td>Comment</td>';
    echo '<td>Working</td>';
    echo '<td>Mode</td>';
    echo '<td>Usuń</td>';
    echo '<td>Dodaj</td>';
    echo '</tr>';

    while ($result = $query->fetch(PDO::FETCH_BOTH)) {
        $id = $result['id'];
        $tableName = $result['tableName'];
        $exeName = $result['exeName'];
        $first = $result['first'];
        $second = $result['second'];
        $word = $result['word'];
        $comment = $result['comment'];
        $working = $result['working'];
        $mode = $result['mode'];

        $colorExeName = "";
        $colorFirst = "";
        $colorSecond = "";

        if ($tableName == 'exeengname') {
            $query2 = "SELECT COUNT(*) AS count FROM exeengname WHERE name='" . addslashes($exeName) . "';";
            $query2 = $db->getQuery($query2);
            $result2 = $query2->fetch(PDO::FETCH_BOTH);
            $count = $result2['count'];

            if ($count > 0) {
                $colorExeName = "bgcolor=\"#990000\"";
            } else {
                $colorExeName = "bgcolor=\"#009900\"";
            }
        }

        if ($tableName == 'exedeuname') {
            $query2 = "SELECT COUNT(*) AS count FROM exedeuname WHERE name='" . addslashes($exeName) . "';";
            $query2 = $db->getQuery($query2);
            $result2 = $query2->fetch(PDO::FETCH_BOTH);
            $count = $result2['count'];

            if ($count > 0) {
                $colorExeName = "bgcolor=\"#990000\"";
            } else {
                $colorExeName = "bgcolor=\"#009900\"";
            }
        }

        if ($tableName == 'exeeng') {
            $query2 = "SELECT COUNT(*) AS count FROM exeengname WHERE name='" . addslashes($exeName) . "';";
            $query2 = $db->getQuery($query2);
            $result2 = $query2->fetch(PDO::FETCH_BOTH);
            $count = $result2['count'];

            if ($count > 0) {
                $colorExeName = "bgcolor=\"#009900\"";
            } else {
                $colorExeName = "bgcolor=\"#990000\"";
            }
        }

        if ($tableName == 'exedeu') {
            $query2 = "SELECT COUNT(*) AS count FROM exedeuname WHERE name='" . addslashes($exeName) . "';";
            $query2 = $db->getQuery($query2);
            $result2 = $query2->fetch(PDO::FETCH_BOTH);
            $count = $result2['count'];

            if ($count > 0) {
                $colorExeName = "bgcolor=\"#009900\"";
            } else {
                $colorExeName = "bgcolor=\"#990000\"";
            }
        }

        if ($tableName == 'eng') {
            $query2 = "SELECT COUNT(*) AS count FROM eng WHERE english='" . addslashes($first) . "';";
            $query2 = $db->getQuery($query2);
            $result2 = $query2->fetch(PDO::FETCH_BOTH);
            $count = $result2['count'];

            if ($count > 0) {
                $colorFirst = "bgcolor=\"#990000\"";
            } else {
                $colorFirst = "bgcolor=\"#009900\"";
            }

            $query2 = "SELECT COUNT(*) AS count FROM eng WHERE polish='" . addslashes($second) . "';";
            $query2 = $db->getQuery($query2);
            $result2 = $query2->fetch(PDO::FETCH_BOTH);
            $count = $result2['count'];

            if ($count > 0) {
                $colorSecond = "bgcolor=\"#990000\"";
            } else {
                $colorSecond = "bgcolor=\"#009900\"";
            }
        }

        if ($tableName == 'deu') {
            $query2 = "SELECT COUNT(*) AS count FROM deu WHERE deutsch='" . addslashes($first) . "';";
            $query2 = $db->getQuery($query2);
            $result2 = $query2->fetch(PDO::FETCH_BOTH);
            $count = $result2['count'];

            if ($count > 0) {
                $colorFirst = "bgcolor=\"#990000\"";
            } else {
                $colorFirst = "bgcolor=\"#009900\"";
            }

            $query2 = "SELECT COUNT(*) AS count FROM deu WHERE polish='" . addslashes($second) . "';";
            $query2 = $db->getQuery($query2);
            $result2 = $query2->fetch(PDO::FETCH_BOTH);
            $count = $result2['count'];

            if ($count > 0) {
                $colorSecond = "bgcolor=\"#990000\"";
            } else {
                $colorSecond = "bgcolor=\"#009900\"";
            }
        }

        if ($tableName == 'exeeng') {
            $query2 = "SELECT COUNT(*) AS count FROM exeeng WHERE first='" . addslashes($first) . "';";
            $query2 = $db->getQuery($query2);
            $result2 = $query2->fetch(PDO::FETCH_BOTH);
            $count = $result2['count'];
            if ($count > 0) {
                $colorFirst = "bgcolor=\"#990000\"";
            } else {
                $colorFirst = "bgcolor=\"#009900\"";
            }
        }

        if ($tableName == 'exedeu') {
            $query2 = "SELECT COUNT(*) AS count FROM exedeu WHERE first='" . addslashes($first) . "';";
            $query2 = $db->getQuery($query2);
            $result2 = $query2->fetch(PDO::FETCH_BOTH);
            $count = $result2['count'];

            if ($count > 0) {
                $colorFirst = "bgcolor=\"#990000\"";
            } else {
                $colorFirst = "bgcolor=\"#009900\"";
            }
        }

        echo '<form action="fromgirls.php" method="post">';
        echo '<tr>';
        echo '<td>' . $id . '</td>';
        echo '<td onclick="editExe(this, ' . $id . ', \'editTableName\');">' . $tableName . '</td>';
        echo '<td onclick="editExe(this, ' . $id . ', \'editExeName\');" ' . $colorExeName . '>' . $exeName . '</td>';
        echo '<td onclick="editExe(this, ' . $id . ', \'editFirst\');" ' . $colorFirst . '>' . $first . '</td>';
        echo '<td onclick="editExe(this, ' . $id . ', \'editSecond\');" ' . $colorSecond . '>' . $second . '</td>';
        echo '<td onclick="editExe(this, ' . $id . ', \'editWord\');">' . $word . '</td>';
        echo '<td onclick="editExe(this, ' . $id . ', \'editComment\');">' . $comment . '</td>';
        echo '<td onclick="editExe(this, ' . $id . ', \'editWorking\');">' . $working . '</td>';
        echo '<td onclick="editExe(this, ' . $id . ', \'editMode\');">' . $mode . '</td>';
        echo '<td><a href="fromgirls.php?delete=' . $id . '">Usuń</a></td>';

        echo '<input type="hidden" name="id" value="' . $id . '" />
    <input type="hidden" name="tableName" value="' . htmlentities($tableName) . '" />
    <input type="hidden" name="exeName" value="' . htmlentities($exeName) . '" />
    <input type="hidden" name="first" value="' . htmlentities($first) . '" />
    <input type="hidden" name="second" value="' . htmlentities($second) . '" />
    <input type="hidden" name="word" value="' . htmlentities($word) . '" />
    <input type="hidden" name="comment" value="' . htmlentities($comment) . '" />
    <input type="hidden" name="working" value="' . $working . '" />
    <input type="hidden" name="mode" value="' . $mode . '" />
    ';
        echo '<td><input type="submit" value="Dodaj" name="add"></td>';
        echo '</tr></form>';


    }
    echo '</table>';
}
else{

    $tableName = $_POST['tableName'];
    $exeName = $_POST['exeName'];
    $first = $_POST['first'];
    $second = $_POST['second'];
    $word = $_POST['word'];
    $comment = $_POST['comment'];
    $working = $_POST['working'];
    $mode = $_POST['mode'];


    $query = "INSERT INTO fromgirls(tableName, exeName, first, second, word, comment, working, mode) VALUES(:tableName, :exeName, :first, :second, :word, :comment, :working, :mode);";
    $stmt = $db->prepareQuery($query);
    $stmt->bindParam(':tableName', $tableName);
    $stmt->bindParam(':exeName', $exeName);
    $stmt->bindParam(':first', $first);
    $stmt->bindParam(':second', $second);
    $stmt->bindParam(':word', $word);
    $stmt->bindParam(':comment', $comment);
    $stmt->bindParam(':working', $working);
    $stmt->bindParam(':mode', $mode);
    $stmt->execute();
}