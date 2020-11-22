<?php
session_start();
require_once("inc/connectDB.php");
require_once("inc/sql.php");

// test retour de saisie du formulaire
// -----------------------------------        

if (isset($_POST['envoi'])) {

    $courriel = trim($_POST['courriel']);
    $mdp      = trim($_POST['mdp']);

    
    $resultat = sqlControlerUtilisateur($oConn, $courriel, $mdp);

    if ($resultat['errSql'] === "") {
        $utilisateur = $resultat['utilisateur'];        
        if (count($utilisateur) !== 0) {
            $_SESSION['utilisateur'] = $utilisateur;
            if (isset($_SESSION['commande'])) {
                header('Location: confirme-commande.php');
                exit;
            } else {
                if (isset($_SESSION['admin'])) {
                    if ($utilisateur['utilisateur_type'] === 'A') {
                        header('Location: admin.php'); 
                        exit;
                    } else {
                        $erreur = "Vous n'êtes pas administrateur de ce site.";
                        unset($_SESSION['utilisateur']);
                    }
                } else {
                    header('Location: index.php');
                    exit;
                }
            }
        } else {
            $erreur = "Identifiant ou mot de passe incorrect.";
        }
    } else {
        $_SESSION['mesErr'] = $resultat['errSql'];
        header('Location: erreur.php');
        exit;
    }
}

// fin du code PHP au-dessus du code HTML
// ======================================

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Authentification">
    <title>Sport et Plein Air | Authentification</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header>
        <h1><a href="index.php">Sport et Plein Air</a></h1>
    </header>
    <main>
        <h2>Authentification</h2>
        <span class="err"><?php echo isset($erreur) ? $erreur : "&nbsp;" ?></span>
        
        <form id="authentification" action="authentification.php" method="post">
            <label>Courriel</label>
            <input type="email"   name="courriel" value="" required>
            <label>Mot de passe</label>
            <input type="password"   name="mdp" value="" required>
            <input type="submit" name="envoi" value="Envoyez"> 
        </form>
        
        <?php if (!isset($_SESSION['admin'])) : ?>
        
        <p>Si vous n'êtes pas déjà  inscrit, <a href="inscription.php">inscrivez-vous</a></p>

        <?php endif; ?>

    </main>
</body>
</html>	
