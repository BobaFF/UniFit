<?php
    
require_once "db/DBAccess.php";
session_start();

$paginaHTML = file_get_contents("html/schedaAdmin.html");

$db = new DBAccess();

if(!isset($_SESSION['logged_in']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if($_SESSION['admin'] == FALSE) {
    header('Location: error404.php');
    exit();
}

// Controlli cookies
$idscheda = $_GET['ids'];

try {
    $db->openDBConnection();
    $userScheda = $db->getUsernameAtletaByScheda($idscheda);
    $atleta = $db->getAtletaByUsername($userScheda);
    $intestazione = $db->getIntestazioneScheda($idscheda);
    $eserciziDb = $db->getEsercizi();
    $esercizi = $db->getEserciziScheda($idscheda);
    $n_esercizi = $db->countEserciziScheda($idscheda);
    $db->closeDBConnection();
} catch (Exception) {
    header('Location: error500.php');
    exit();
}

if (isset($_GET['success'])) {
    if($_GET['success'] == 1) {
        $strSuccess = "<p class=\"messaggioAR successo\">Modifica effettuata con successo!</p>";
        $paginaHTML = str_replace("{messaggioModifica}", $strSuccess, $paginaHTML);
    } elseif($_GET['success'] == 2) {
        $strSuccess = "<p class=\"messaggioAR successo\">Scheda creata con successo!</p>";
        $paginaHTML = str_replace("{messaggioModifica}", $strSuccess, $paginaHTML);
    } else {
        $strUnsuccess = "<p class=\"messaggioAR errore\">Errore durante la modifica della scheda!</p>";
        $paginaHTML = str_replace("{messaggioModifica}", $strUnsuccess, $paginaHTML); 
    }
} else $paginaHTML = str_replace("{messaggioModifica}", " ", $paginaHTML);

if (isset($_POST['editPeriodo'])) {
    pulisciInput($_POST);
    $inizio = strtotime($_POST['inizioS']);
    $fine = strtotime($_POST['fineS']);
    if($inizio>$fine) {
        header(('Location: schedaAdmin.php?ids='.$idscheda.'&success=0'));
        exit();
    }
    try {
        $db->openDBConnection();
        $db->updateInizioScheda($idscheda, $_POST['inizioS']);
        $db->updateFineScheda($idscheda, $_POST['fineS']);
        $db->closeDBConnection();
        header(('Location: schedaAdmin.php?ids='.$idscheda.'&success=1'));
        exit();
    } catch (Exception) {
        header(('Location: schedaAdmin.php?ids='.$idscheda.'&success=0'));
        exit();
    }
}

if (isset($_POST['edit'])) {
    try {
        $db->openDBConnection();
        pulisciInput($_POST);
        for ($i = 1; $i<=$n_esercizi; $i++) {
            $ide = $_POST['idesercizio_'.$i];
            $tipoEs = $_POST['tipo_'.$i];
            $ripetizioniEs = $_POST['ripetizioni_'.$i];
            $recuperoEs = $_POST['recupero_'.$i];
            $note = $_POST['note_'.$i];
            if(empty($tipoEs)!= "")
                throw new Exception();
            $db->updateTipologiaEsercizio($idscheda, $ide, $tipoEs);
            
            if(!checkRipetizioni($ripetizioniEs))
                throw new Exception();
            $db->updateRipetizioniEsercizio($idscheda, $ide, $ripetizioniEs);
            
            if(!checkRecupero($recuperoEs))
                throw new Exception();
            $db->updateRecuperoEsercizio($idscheda, $ide, $recuperoEs);
            
            if(!checkNote($note))
                throw new Exception();
            $db->updateNoteEsercizio($idscheda, $ide, $note);
        }
        $db->closeDBConnection();
        header(('Location: schedaAdmin.php?ids='.$idscheda.'&success=1'));
        exit();
    } catch (Exception) {
        header(('Location: schedaAdmin.php?ids='.$idscheda.'&success=0'));
        exit();
    }
}

if (isset($_POST['add'])) {
    pulisciInput($_POST);
    try {
        if(!($_POST['new_esercizio'])|| !checkRipetizioni($_POST['new_ripetizioni']) || !checkRecupero($_POST['new_recupero']) || !checkNote($_POST['new_note']))
            throw new Exception;
        $db->openDBConnection();
        $db->createEsercizioScheda($idscheda, $_POST['new_esercizio'], $_POST['new_ripetizioni'], $_POST['new_recupero'], $_POST['new_note']);
        $db->closeDBConnection();
        header(('Location: schedaAdmin.php?ids='.$idscheda.'&success=1'));
        exit();
    } catch (Exception) {
        //Errore nell'aggiornamento della scheda
        header(('Location: schedaAdmin.php?ids='.$idscheda.'&success=0'));
    }
} 

if (isset($_POST['rimuoviE'])) {
    pulisciInput($_POST);
    try {
        if(!$_POST['del_esercizio'])
            throw new Exception;
        $db->openDBConnection();
        $db->deleteEsercizioFromScheda($_POST['del_esercizio'], $idscheda);
        $db->closeDBConnection();
        header(('Location: schedaAdmin.php?ids='.$idscheda.'&success=1'));
        exit();
    } catch (Exception) {
        //Errore nell'aggiornamento della scheda
        header(('Location: schedaAdmin.php?ids='.$idscheda.'&success=0'));
    }
}

if(isset($_POST['eliminaScheda'])) {
    pulisciInput($_POST);
    try {
        $db->openDBConnection();
        $db->deleteScheda($idscheda);
        $db->closeDBConnection();
        header('Location: admin.php?status=delScheda');
        exit();
    } catch (Exception) {
        header(('Location: schedaAdmin.php?ids='.$idscheda.'&success=0'));
    }
}
$strNome = $atleta['cognome']." ". $atleta['nome'];
$strScheda = "";
$strFormModifica = "<form method = \"post\" action=\"schedaAdmin.php?ids=$idscheda\" id=\"formModificaEsercizi\">
<input type=\"hidden\" name =\"idscheda\" id=\"idscheda\" value = $idscheda>";
$strFormRimuovi = "";
$count = 1;
foreach($esercizi as $esercizio) {
    $strScheda.=           "<tr>
                                <th scope =\"row\">$count</th>
                                <td><span lang = \"en\">".$esercizio['esercizio']."</span></td>".
                                "<td><img alt = \"Immagine dimostrativa dell'esercizio\" src =\"".$esercizio['immagine']."\"></td>".
                                "<td data-title=\"Ripetizioni\">".$esercizio['ripetizioni']."</td>".
                                "<td data-title=\"Recupero\">".$esercizio['recupero']."</td>".
                                "<td data-title=\"Note\">".$esercizio['note']."</td>
                                </tr>";
    
    $strFormModifica.=              "<fieldset>
                            <legend>Esercizio $count</legend>
                            <input type=\"hidden\" name=\"idesercizio_$count\" id=\"idesercizio_$count\" value=".$esercizio['ide'].">	
                            <label for=\"tipo_$count\">Esercizio:</label>	
                            <select name=\"tipo_$count\" id=\"tipo_$count\" class=\"esercizioForm\">	
                                <option value = \"".$esercizio['esercizio']."\" selected = \"selected\"><span lang=\"en\">".$esercizio['esercizio']."</span> --corrente</option>
                                {esercizi}	
                            </select>
                            <label for=\"ripetizioni_$count\">Ripetizioni:</label>
                            <textarea form = \"formModificaEsercizi\"id=\"ripetizioni_".$count."\" name=\"ripetizioni_".$count."\" class=\"ripetizioneForm\">".$esercizio['ripetizioni']."</textarea>
                            <label for=\"recupero_$count\">Recupero:</label>
                            <textarea form = \"formModificaEsercizi\"id=\"recupero_".$count."\" name=\"recupero_".$count."\" class=\"recuperoForm\">".$esercizio['recupero']."</textarea>
                            <label for=\"note_".$count."\">Note</label>
                            <textarea form = \"formModificaEsercizi\"id=\"note_".$count."\" name=\"note_".$count."\" class=\"notaForm\">".$esercizio['note']."</textarea>
                            </fieldset>";
    $strFormRimuovi.=       "<option value = \"".$esercizio['ide']."\">$count - ".$esercizio['esercizio']."</option>";          
    $count++;
}

$strFormModifica .= "<input type=\"submit\" name=\"edit\" id=\"edit\" value=\"Conferma modifiche\"></form>";

$strEsercizi = "";
foreach($eserciziDb as $esercizioDb) {
    $strEsercizi .=   "<option value = \"".$esercizioDb['nomeesercizio']."\"><span lang=\"en\">".$esercizioDb['nomeesercizio']."</span></option>";
}


$paginaHTML = str_replace("{nomeAtleta}", $strNome, $paginaHTML);
$paginaHTML = str_replace("{nEsercizi}", $strFormRimuovi, $paginaHTML);
$paginaHTML = str_replace("{idScheda}", $idscheda, $paginaHTML);
$paginaHTML = str_replace("{scheda}", $strScheda, $paginaHTML);
$paginaHTML = str_replace("{nome istruttore}", $intestazione['cognome']." ".$intestazione['nome'], $paginaHTML);
$paginaHTML = str_replace("{inizio}", $intestazione['inizio'], $paginaHTML);
$paginaHTML = str_replace("{fine}", $intestazione['fine'], $paginaHTML);
$paginaHTML = str_replace("{form}", $strFormModifica, $paginaHTML);
$paginaHTML = str_replace("{esercizi}", $strEsercizi, $paginaHTML);
echo $paginaHTML;
    
?>
