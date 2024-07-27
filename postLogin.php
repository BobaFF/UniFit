<?php
require_once "db/DBAccess.php";

// Verifica se sono stati inviati i dati del modulo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accedi'])) {
    // Verifica se sono stati inviati entrambi username e password
    pulisciInput($_POST);
    $username = $_POST['username'];
    $password = $_POST['password'];
    if(!checkUsername($username) || !checkPassword($password)) {
        header('Location: login.php?status=error');
        exit();
    }
    
    $db = new DBAccess();
    
    try {
            $db->openDBConnection();
            $result = $db->login($username, $password);
            $db->closeDBConnection();
        } catch (Exception $e) {
            header('Location: error500.php');
            exit;
        }

    if(!$result) {
        header('Location: login.php?status=error');
            exit;
    } else {
        // Login riuscito, inizia la sessione
        session_start();
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['nome'] = $result['nome'];
        $_SESSION['cognome'] = $result['cognome'];
        $_SESSION['admin'] = $result['istruttore'];
        $_SESSION['email'] = $result['email'];
        
        if (!$_SESSION['admin'])
            header('Location: user.php?status=login');
        else header('Location: admin.php?status=login');
        exit;
    }
}
else if(isset($_SESSION['logged_in']) || isset($_SESSION['username'])) {
    $_SESSION['admin'] == 1? header('Location: admin.php'): header('Location: user.php');
    exit();
}
else header('Location: index.php');

?>
