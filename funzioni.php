<?php

/**
* Pulisce una stringa in input
*/
function pulisciInputHelper(&$value) {
    if($value) {
        $value = trim($value); //rimuove gli spazi bianchi (o altri caratteri) dall'inizio e dalla fine di una stringa
        $value = strip_tags($value); //strip_tags() rimuove le tag HTML e PHP da una stringa
        $value = htmlentities($value); //htmlentities() converte i caratteri speciali in entità HTML
    }
}

/**
* Gestisce la pulizia degli input (array o stringhe)

*/
function pulisciInput(&$value) {
    if(is_array($value)) {
        array_walk($value, function (&$v) {
            pulisciInputHelper($v);

        });  
    }
    else pulisciInputHelper($value);
}

// FORM login e signup

/**
 * Controlla l'email inserita in un form
 * @param $email
 * @return bool
 */
function checkEmail($email): bool {
    if(strlen($email)<=127 && strlen($email)>=8) {
        return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }
    else return false;
}

function checkUsername($username): bool {
    if(preg_match('/^[a-zA-Z]{4,20}$/', $username))
        return true;
    else return false;
}

function checkNome($nome): bool {
    if(preg_match('/^[a-zA-Z]{3,63}$/', $nome))
        return true;
    else return false;
}

function checkPassword($password): bool {
    if(preg_match('/^[^\s]{4,63}$/', $password))
        return true;
    else return false;
}

//FORM Scheda

function checkRipetizioni($ripetizioni): bool {
    if(preg_match('/^[-().a-zA-Z*×\d\s]{3,40}$/', $ripetizioni))
        return true;
    else return false;
}

function checkRecupero($recupero): bool {
    if(preg_match('/^[-().a-zA-Z*×\d\s]{3,40}$/', $recupero))
        return true;
    else return false;
}

function checkNote($note): bool {
    if(preg_match('/^[\w\d\s\S]{0,500}$/', $note))
        return true;
    else return false;
}

//NEl controllo: o espressione regolare oppure Filtro

//Le stringhe vanno pulite prima di passarle nel form.

?>