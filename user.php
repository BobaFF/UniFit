<?php

require_once ("db/DBAccess.php");
session_start();


$paginaHTML = file_get_contents("html/user.html");

$db = new DBAccess();

if(!isset($_SESSION['logged_in']) || !isset($_SESSION['username']) || $_SESSION['admin'] == TRUE) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];


$strLogin = "<p class = \"messaggioAR successo\">Login effettuato con successo!</p>";
$strSignup = "<p class = \"messaggioAR successo\">Registrazione avvenuta con successo!</p>";
if(isset($_GET['status']) && $_GET['status']=='login')
    $paginaHTML=str_replace("<!--{avvisoUser}-->", $strLogin, $paginaHTML);
elseif(isset($_GET['status']) && $_GET['status']=='signup')
    $paginaHTML=str_replace("<!--{avvisoUser}-->", $strSignup, $paginaHTML);

try {
    $db->openDBConnection();
    $atleta = $db->getAtletaByUsername($username);
    $schedeValide = $db->getSchedeCorrentiAtleta($username);
    $schedeScadute = $db->getSchedeScaduteAtleta($username);
    $db->closeDBConnection();
} catch (Exception) {
    header('Location: error500.php');
    exit();
}

if (empty($schedeValide)) 
    $strSchedeValide = "<p class=\"listaSchede\">Nessuna scheda presente</p>";
else {
    $strSchedeValide = "<ul class=\"listaSchede\">";
    foreach ($schedeValide as $scheda) {
        $strSchedeValide .= "<li>
                            <a href = \"scheda.php?ids=".$scheda['ids']."\">
                            Scheda n.".$scheda['ids']." valida dal 
                            <time datetime =\"".$scheda['inizio']."\">".$scheda['inizio']."</time> 
                            al <time datetime =\"".$scheda['fine']."\">".$scheda['fine']."</time>
                            </a>
                            </li>";
    }
    $strSchedeValide .= "</ul>";
}

if (empty($schedeScadute)) 
    $strSchedeScadute = "<p class=\"listaSchede\">Nessuna scheda presente</p>";
else {
    $strSchedeScadute = "<ul class=\"listaSchede\">";
    foreach ($schedeScadute as $scheda) {
        $strSchedeScadute .= "<li>
                            <a href = \"scheda.php?ids=".$scheda['ids']."\">
                            Scheda n.".$scheda['ids']." valida dal 
                            <time datetime =\"".$scheda['inizio']."\">".$scheda['inizio']."</time> 
                            al <time datetime =\"".$scheda['fine']."\">".$scheda['fine']."</time>
                            </a>
                            </li>";
    }
    $strSchedeScadute .= "</ul>";
}
//Form modifica dati
$strSuccess = "<p class = \"messaggioAR successo\">Modifica effettuata con successo</p>";
$strError = ["<p class = \"messaggioAR errore\">Impossibile modificare: email non valida</p>", 
"<p class = \"messaggioAR errore\">Impossibile modificare: email uguale alla precedente</p>",
"<p class = \"messaggioAR errore\">Impossibile modificare: email già utilizzata</p>"];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['modificaMail'])) {
        pulisciInput($_POST['newEmail']);
        $email = $_POST['newEmail'];
        if (!checkEmail($email))
            $paginaHTML = str_replace("<!--{avvisoMail}-->", $strError[0], $paginaHTML);
        elseif ($_POST['oldEmail']==$email) 
            $paginaHTML = str_replace("<!--{avvisoMail}-->", $strError[1], $paginaHTML);
        else {
            try {
                $db->openDBConnection();
                $db->updateMailUser($username, $email);
                $db->closeDBConnection();
                $paginaHTML = str_replace("<!--{avvisoMail}-->", $strSuccess, $paginaHTML);
                $_SESSION['email'] = $_POST['newEmail'];
            } catch (Exception) {
                $paginaHTML = str_replace("<!--{avvisoMail}-->", $strError[2], $paginaHTML);
            }
        } 
    }
}
$footer = file_get_contents("html/footer.html");
$paginaHTML = str_replace("{footer}", $footer, $paginaHTML);
$paginaHTML = str_replace("{schedeValide}", $strSchedeValide, $paginaHTML);
$paginaHTML = str_replace("{schedeScadute}", $strSchedeScadute, $paginaHTML);
$paginaHTML = str_replace("{cognomeAtleta}",$_SESSION['cognome'], $paginaHTML);
$paginaHTML = str_replace("{nomeAtleta}",$_SESSION['nome'], $paginaHTML);
$paginaHTML = str_replace("{usernameAtleta}",$_SESSION['username'], $paginaHTML);
$paginaHTML = str_replace("{emailAtleta}",$_SESSION['email'], $paginaHTML);
echo $paginaHTML;

?>