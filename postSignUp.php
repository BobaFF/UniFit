<?php
require_once "db/DBAccess.php";
session_start();

$db = new DBAccess();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['username']) && isset($_POST['password'])) {
        pulisciInput($_POST);
        $username = $_POST['username'];
        $nome = $_POST['name'];
        $cognome = $_POST['surname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        if(!checkUsername($username) || !checkNome($nome) || !checkNome($cognome) || !checkEmail($email) || !checkPassword($password)) {
            header('Location: signup.php?status=error');
            exit();
        }
        try {
            $db->openDBConnection();
            $result = $db->signup($username, $nome, $cognome, $email, $password);
            $db->closeDBConnection();
        }catch(Exception $err) {
            header('Location: signup.php?status=error');
            exit();
        }
        session_start();
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['nome'] = $nome;
        $_SESSION['cognome'] = $cognome;
        $_SESSION['email'] = $email;
        $_SESSION['admin'] = false;
        header('Location: user.php?status=signup');
        
    }
else if(isset($_SESSION['logged_in']) || isset($_SESSION['username'])) {
        $_SESSION['admin'] == 1? header('Location: admin.php'): header('Location: user.php');
        exit();
    }
else header('Location: index.php');
}
?>