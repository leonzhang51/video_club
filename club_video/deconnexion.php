<?php
session_start();
unset($_SESSION['utilisateur']);
unset($_SESSION['commande']);

if (isset($_SESSION['admin'])) {
    header('Location: authentification.php'); 
} else {
    header('Location: index.php'); 
}
?>