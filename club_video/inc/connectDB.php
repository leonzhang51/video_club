<?php
  
require_once("paramConnectDB.php");

$oConn = new mysqli(HOST, USER, PASSWORD, DBNAME);

if ($oConn->connect_errno) {
    $_SESSION['mesErr'] = $oConn->connect_errno." - ".utf8_encode($oConn->connect_error);
    header('Location: erreur.php');
    exit;
}

$oConn->set_charset("utf8");