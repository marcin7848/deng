<?php

if(date('H') >= 9 &&  date('H') <= 23){

require_once './db_connect.php';
$db = new db_connect();

$query = "SELECT * FROM eng WHERE id NOT IN (SELECT id_eng FROM send_email) ORDER BY RAND() LIMIT 1;";
$query = $db->getQuery($query);
$result = $query->fetch(PDO::FETCH_BOTH);

$id = $result['id'];
$pl = $result['polish'];
$eng = $result['english'];


$query = "INSERT INTO send_email(id, id_eng) VALUES('', :id);";
$stmt = $db->prepareQuery($query);
$stmt->bindParam(':id', $id);
$stmt->execute();

$to = 'marcin7848@gmail.com';
$subject = 'Ang'; 
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$message = 'Wymysl 3 zdania z slowem: <br /> <b>'.$eng.'</b> - <b>'.$pl.'</b>';

mail($to, $subject, $message, $headers);

}


?>	