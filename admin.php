<?php

require_once ("db/DBAccess.php");
session_start();


$paginaHTML = file_get_contents("html/admin.html");

$db = new DBAccess();

if(!isset($_SESSION['logged_in']) || !isset($_SESSION['username']) || $_SESSION['admin'] == FALSE) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

try {
    $db->openDBConnection();
    $atleti = $db->getListaAtleti();
    $eserciziDb = $db->getEsercizi();
    $db->closeDBConnection();
} catch (Exception) {
    header('Location: error500.php');
    exit();
}

$strLogin = "<p class=\"messaggioAR successo\">Login effettuato con successo!</p>";
$strEliminaUser = "<p class=\"messaggioAR successo\">Utente eliminato con successo!</p>";
$strEliminaScheda = "<p class=\"messaggioAR successo\">Scheda eliminata con successo!</p>";
if(!isset($_GET['status']))
    $paginaHTML=str_replace("{avvisoUser}", "", $paginaHTML);
else {
    switch ($_GET['status']) {
        case 'login':
            $paginaHTML=str_replace("<!--{avvisoAdmin}-->", $strLogin, $paginaHTML);
            break;
        case 'delUser':
            $paginaHTML=str_replace("<!--{avvisoAdmin}-->", $strEliminaUser, $paginaHTML);
            break;
        case 'delScheda':
            $paginaHTML=str_replace("<!--{avvisoAdmin}-->", $strEliminaScheda, $paginaHTML);
            break;
        default: 
            break;
    }
}


//Atleti
$strAtleti = "";
foreach($atleti as $atleta) {
    $strAtleti .=   "<option value = \"".$atleta['username']."\">".$atleta['cognome']." ".$atleta['nome']."</option>";
}

//Esercizi
$strEsercizi = "";
foreach($eserciziDb as $esercizioDb) {
    $strEsercizi .=   "<option value = \"".$esercizioDb['nomeesercizio']."\" lang=\"en\">".$esercizioDb['nomeesercizio']."</option>";
}

$strAvvisoCreazione = "";
if(isset($_GET['success']) && $_GET['success'] == 0) 
    $strAvvisoCreazione = "<p class=\"messaggioAR errore\">Errore nella creazione della scheda</p>";

//FORM crea scheda
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['creaSchedaBtn']) && !empty($_POST['Atleta'])) {
        $atleta = $_POST['Atleta'];
        pulisciInput($_POST);
        $inizio = strtotime($_POST['inizio']);
        $fine = strtotime($_POST['fine']);
        if($inizio>$fine) {
            header(('Location: admin.php?success=0#creazioneScheda'));
        exit();
        }
        $esercizi = 0;
        for($i = 1; $i<=10; $i++) {
            if(!empty($_POST['esercizio'.$i]) && $_POST['esercizio'.$i] != "") {
            $result['esercizio'][$i] = $_POST['esercizio'.$i];
            $result['ripetizioni'][$i] = $_POST['ripetizioni'.$i];
            $result['recupero'][$i] = $_POST['recupero'.$i];
            $result['note'][$i] = $_POST['note'.$i];
            $esercizi++;}
        }
        try {
            if($esercizi == 0)
                throw new Exception;
            $db->openDBConnection();
            $scheda = $db->createIntestazioneScheda($atleta, $_SESSION['username'], $_POST['inizio'], $_POST['fine']);
            for ($i = 1; $i<=$esercizi; $i++) {
                if(!checkRipetizioni($result['ripetizioni'][$i])|| !checkRecupero($result['recupero'][$i]) ||!checkNote($result['note'][$i])) {
                    $db->deleteScheda($scheda);
                    throw new Exception;
                }
                $db->createEsercizioScheda($scheda, $result['esercizio'][$i], $result['ripetizioni'][$i], $result['recupero'][$i], $result['note'][$i]);
            }
            $db->closeDBConnection();
            header(('Location: schedaAdmin.php?ids='.$scheda.'&success=2'));
        } catch (Exception ) {
            header(('Location: admin.php?success=0#creazioneScheda'));
        }
        exit();
    }
}

//FORM cerca scheda
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cercaSchedaBtn'])) {
    if(!empty($_POST['nomeAtleta'])) {
        try {
            $db->openDBConnection();
            $schedeValide = $db->getSchedeCorrentiAtleta($_POST['nomeAtleta']);
            $schedeScadute = $db->getSchedeScaduteAtleta($_POST['nomeAtleta']);
            $db->closeDBConnection();
        } catch (Exception) {
            header('Location: error500.php');
            exit();
        }
        $strAtleti = str_replace("\"".$_POST['nomeAtleta']."\"", "\"".$_POST['nomeAtleta']."\" selected",  $strAtleti);
        if (empty($schedeValide)) 
            $strSchedeValide = "<h3>Schede valide</h3>
                                <p class=\"listaSchede\">Nessuna scheda presente</p>";
        else {
        $strSchedeValide = "<h3>Schede valide</h3><ul class=\"listaSchede\">";
        foreach ($schedeValide as $scheda) {
            $strSchedeValide .= "<li>
                            <a href = \"schedaAdmin.php?ids=".$scheda['ids']."\">
                            Scheda n.".$scheda['ids']." valida dal 
                            <time datetime =\"".$scheda['inizio']."\">".$scheda['inizio']."</time> 
                            al <time datetime =\"".$scheda['fine']."\">".$scheda['fine']."</time>
                            </a>
                            </li>";
        }
        $strSchedeValide .= "</ul>";
        }

        if (empty($schedeScadute)) 
            $strSchedeScadute = "<h3>Schede scadute</h3><p class =\"listaSchede\">Nessuna scheda presente</p>";
        else {
            $strSchedeScadute = "<h3>Schede scadute</h3><ul class=\"listaSchede\">";
            foreach ($schedeScadute as $scheda) {
                $strSchedeScadute .= "<li>
                            <a href = \"schedaAdmin.php?ids=".$scheda['ids']."\">
                            Scheda n.".$scheda['ids']." valida dal 
                            <time datetime =\"".$scheda['inizio']."\">".$scheda['inizio']."</time> 
                            al <time datetime =\"".$scheda['fine']."\">".$scheda['fine']."</time>
                            </a>
                            </li>";
            }
        $strSchedeScadute .= "</ul>";
        }
    }
    else echo "errore";
}
else {
    $strSchedeValide = "";
    $strSchedeScadute = "";
}

if(($_SERVER['REQUEST_METHOD']== "POST") && isset($_POST['eliminaUtente'])) {
    if(!empty($_POST['eliminaUtente'])) {
        try {
            $db->openDBConnection();
            $db->deleteAtleta($_POST['listaUtenti']);
            $db->closeDBConnection();
            header('Location: admin.php?status=delUser');
            exit();
        } catch(Exception) {
            header("Location: admin.php?success=0");
        }
    }
}

/*$strSchedeValide = empty($strSchedeValide)? "" : $strSchedeValide;
$strSchedeScadute = empty($strSchedeScadute)? "" : $strSchedeScadute;
*/
$footer = file_get_contents("html/footer.html");
$paginaHTML = str_replace("{messaggioCreazione}", $strAvvisoCreazione, $paginaHTML);
$paginaHTML = str_replace("{esercizi}", $strEsercizi, $paginaHTML);
$paginaHTML = str_replace("{atleti}", $strAtleti, $paginaHTML);
$paginaHTML = str_replace("<!--{schedeValide}-->", $strSchedeValide, $paginaHTML);
$paginaHTML = str_replace("<!--{schedeScadute}-->", $strSchedeScadute, $paginaHTML);
echo $paginaHTML;
?>