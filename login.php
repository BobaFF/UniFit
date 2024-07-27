<?php
session_start();
if(isset($_SESSION['logged_in']) || isset($_SESSION['username'])) {
    $_SESSION['admin'] == 1? header('Location: admin.php'): header('Location: user.php');
    exit();
}
$paginaHTML = file_get_contents("html/login.html");
$strLogout = "<p class=\"messaggioAR successo\" ><span lang=\"en\">Logout</span> effettuato con successo.</p>";
$strError = "<p class=\"messaggioAR errore\">Credenziali errate.</p>";

if(isset($_GET['status']) && $_GET['status']=='logout')
    $paginaHTML=str_replace("<!--{avvisoLogin}-->", $strLogout, $paginaHTML);
elseif(isset($_GET['status']) && $_GET['status']=='error')
    $paginaHTML=str_replace("<!--{avvisoLogin}-->", $strError, $paginaHTML);

$footer = file_get_contents("html/footer.html");
$paginaHTML = str_replace("{footer}", $footer, $paginaHTML);
echo $paginaHTML;

?>