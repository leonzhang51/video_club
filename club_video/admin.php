<?php
session_start();
require_once("inc/connectDB.php");
require_once("inc/sql.php");

$_SESSION['admin'] = TRUE;
$utilisateur = isset($_SESSION['utilisateur']) ? $_SESSION['utilisateur'] : "";
	
if ($utilisateur === "" || $utilisateur['utilisateur_type'] !== "A") {
    unset($_SESSION['utilisateur']);
    unset($_SESSION['commande']);
    header('Location: authentification.php');
    exit;
}

// fin du code PHP au-dessus du code HTML
// ======================================

?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Sport et Plein Air | Administration</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>

    <header>
        <h1>Sport et Plein Air</h1>
        <ul>
            <li><?= $utilisateur !== "" ? "Bonjour ".$utilisateur['utilisateur_prenom']." ".$utilisateur['utilisateur_nom']: ""?></li>
            <li><a href="deconnexion.php">Déconnexion</a></li>
        </ul>
    </header>
    <main>
        <pre>Fonctionnalités pour l'administrateur à développer.</pre>
    </main>    
    
</body>
</html>