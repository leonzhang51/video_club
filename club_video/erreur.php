<?php
session_start();
$mesErr = isset($_SESSION['mesErr']) ? $_SESSION['mesErr'] : "";
unset($_SESSION['mesErr']);

// fin du code PHP au-dessus du code HTML
// ======================================

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Erreur technique">
    <title>Sport et Plein Air | Erreur technique</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header>
        <h1><a href="index.php">Sport et Plein Air</a></h1>
    </header>
    <main>
        <h2>Erreur technique</h2>
        <p class="err"><?= $mesErr ?></p>
        
    </main>
</body>
</html>	
