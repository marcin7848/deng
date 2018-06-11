<?php
header('Content-type: text/html; charset=UTF-8');
set_time_limit(0);

require_once 'config.php';
require_once './Controller/controller.php';

session_start();

if (isset($_POST['login'])) {
    $_SESSION['login'] = addslashes($_POST['login']);
}

if ($_SESSION['login'] == 46351) {
    $controller = new controller();
} else {
    echo "<center><form action='index.php' method='POST'>Podaj hasło:<input style='height: 20px; border: 1px solid #000000;' type='password' name='login' size='15' maxlength='15' autocomplete='off'/></form></center>";
}
