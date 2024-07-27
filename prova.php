<?php
require_once "db/DBAccess.php";

$password = "admin";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$db = new DBAccess();
try{
    $db->openDBConnection();
    echo "OK";
}
catch (Exception) {
    echo "ERRORE";
}
?>