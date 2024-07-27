<?php
session_start();
if(!isset($_SESSION)) {
    header('Location: index.php');
} else {
    session_destroy();
    header('Location: login.php?status=logout');
    exit();
}

?>