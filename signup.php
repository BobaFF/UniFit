<?php
session_start();
if(isset($_SESSION['logged_in']) || isset($_SESSION['username'])) {
    $_SESSION['admin'] == 1? header('Location: admin.php'): header('Location: user.php');
    exit();
}

$paginaHTML = file_get_contents("html/signup.html");

$strError = "<p class=\"messaggioAR errore\">Errore in fase di registrazione.</p>";
if(isset($_GET['status']) && $_GET['status']=='error')
    $paginaHTML=str_replace("<!--{avvisoSignup}-->", $strError, $paginaHTML);
$footer = file_get_contents("html/footer.html");
$paginaHTML = str_replace("{footer}", $footer, $paginaHTML);
echo $paginaHTML;

?>