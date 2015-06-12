<?php

function puliscistringa($stringa){
    $stringa = str_replace("à", "a", $stringa);
    $stringa = str_replace("è", "è", $stringa);
    $stringa = str_replace("ì", "i", $stringa);
    $stringa = str_replace("ò", "o", $stringa);                        
    $stringa = str_replace("ù", "u", $stringa);
    $stringa = str_replace("\n", " ", $stringa);
    $stringa = str_replace(" ", "", $stringa);
    //@$stringa = ereg_replace("[^A-Za-z0-9' ]", "", $stringa );
    return $stringa;
}

function mageggia($stringa){
    $stringa = str_replace("/Volumes/", "", $stringa);
    $stringa = str_replace("/", ":", $stringa);
    return $stringa;
}

//Funzione che logga un errore per tacciare tutto quello che succede
// si puo controllare da teminale con: tail -f /Applications/MAMP/logs/php_error.log
function log_stamp($stringa){
    error_log( $stringa , 0);
}

?>