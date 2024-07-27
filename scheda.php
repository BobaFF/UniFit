<?php
    
require_once "db/DBAccess.php";
session_start();

$paginaHTML = file_get_contents("html/scheda.html");

$db = new DBAccess();

if(!isset($_SESSION['logged_in']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if($_SESSION['admin'] == TRUE) {
    header('Location: error404.php');
    exit();
}

// Controlli cookies

$idscheda = $_GET['ids'];

try {
    $db->openDBConnection();
    $userScheda = $db->getUsernameAtletaByScheda($idscheda);
    $intestazione = $db->getIntestazioneScheda($idscheda);
    $esercizi = $db->getEserciziScheda($idscheda);
    $n_esercizi = $db->countEserciziScheda($idscheda);
    $db->closeDBConnection();
} catch (Exception) {
    header('Location: error500.php');
    exit();
}

if (!$userScheda== $_SESSION['username']) {
    header('Location: error404.php');
    exit();
}

if (isset($_GET['success'])) {
    if($_GET['success'] == 1) {
        $strSuccess = "<p class =\"messaggioAR successo\">Modifica effettuata con successo!</p>";
        $paginaHTML = str_replace("<!--{messaggioModifica}-->", $strSuccess, $paginaHTML);
    } else {
        $strUnsuccess = "<p class =\"messaggioAR errore\">Errore durante la modifica della scheda!</p>";
        $paginaHTML = str_replace("<!--{messaggioModifica}-->", $strUnsuccess, $paginaHTML); 
    }
}

if (isset($_POST['edit'])) {
    try {
        $db->openDBConnection();
        for ($i = 1; $i<=$n_esercizi; $i++) {
            $ide = $_POST['idesercizio_'.$i];
            $note = $_POST['note_'.$i];
            if (checkNote($note))
                $db->updateNoteEsercizio($idscheda, $ide, $note);
            else throw new Exception;
        }
        $db->closeDBConnection();
        header(('Location: scheda.php?ids='.$idscheda.'&success=1'));
        exit();
    } catch (Exception) {
        header(('Location: scheda.php?ids='.$idscheda.'&success=0'));
    }
}

$strScheda = "";
$strForm = "<form method = \"post\" action=\"scheda.php?ids=$idscheda\" id=\"editForm\">
<p>Ogni nota può contenere al massimo 500 caratteri.</p>
<input type=\"hidden\" name =\"idscheda\" id=\"idscheda\" value = $idscheda>";
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
    
    $strForm.=              "<fieldset>
                            <legend>Esercizio $count: <span lang=\"en\">".$esercizio['esercizio']."</span></legend>
                            <input type=\"hidden\" name=\"idesercizio_$count\" id=\"idesercizio_$count\" value=".$esercizio['ide'].">	
                            <label for=\"note_".$count."\">Note</label>
                            <textarea form = \"editForm\"id=\"note_".$count."\" name=\"note_".$count."\">".$esercizio['note']."</textarea>
                            </fieldset>";            
    $count++;
}

$strForm .= "<input type=\"submit\" name=\"edit\" id=\"edit\" value=\"Conferma\"></form>";


$footer = file_get_contents("html/footer.html");
$paginaHTML = str_replace("{footer}", $footer, $paginaHTML);
$paginaHTML = str_replace("{idScheda}", $idscheda, $paginaHTML);
$paginaHTML = str_replace("{scheda}", $strScheda, $paginaHTML);
$paginaHTML = str_replace("{nome istruttore}", $intestazione['cognome']." ".$intestazione['nome'], $paginaHTML);
$paginaHTML = str_replace("{inizio}", $intestazione['inizio'], $paginaHTML);
$paginaHTML = str_replace("{fine}", $intestazione['fine'], $paginaHTML);
$paginaHTML = str_replace("{form}", $strForm, $paginaHTML);
echo $paginaHTML;
    
?>
