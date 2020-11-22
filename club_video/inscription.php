<?php
session_start();
require_once("inc/connectDB.php");
require_once("inc/sql.php");

// 1 test retour de saisie du formulaire
// _____________________________________

if (isset($_POST['envoi'])) {

    // contrôles des champs saisis
    // ___________________________
    
    $nom      = trim($_POST['nom']);
    $prenom   = trim($_POST['prenom']);
    $courriel = trim($_POST['courriel']);
    $mdp      = trim($_POST['mdp']);
    $mdp2     = trim($_POST['mdp2']);
       
    $erreurs = array();
    
    if (!preg_match('/^[a-z àéèêô]+$/i', $nom)) {
        $erreurs['nom'] = "Nom incorrect.";
    }
    
    if (!preg_match('/^[a-z àéèêô]+$/i', $prenom)) {
        $erreurs['prenom'] = "Nom incorrect.";
    }
    
    if (!filter_var($courriel, FILTER_VALIDATE_EMAIL)) {
        $erreurs['courriel'] = "Adresse courriel incorrecte.";
    }
    
    $resultat = sqlLireTable($oConn, 'utilisateurs', 'utilisateur_courriel', $courriel);
    if ($resultat['errSql'] === "") {
        if (count($resultat['lignes']) > 0) {
            $erreurs['courriel'] = "Adresse courriel déjà utilisée.";
        }
    } else {
        $_SESSION['mesErr'] = $resultat['errSql'];
        header('Location: erreur.php');
        exit;
    }
    
    if (strlen($mdp) < 8 || !preg_match('/[0-9]+/', $mdp) || !preg_match('/[A-Z]+/', $mdp) || !preg_match('/[a-z]+/', $mdp)) {
        $erreurs['mdp'] = "Le mot de passe doit comporter au moins 8 caractères, un chiffre, une lettre majuscule et une lettre minuscule.";
    }
    
    if ($mdp !== $mdp2) {
        $erreurs['mdp2'] = "Ce mot de passe est différent du premier saisi.";
    }
    
    // enregistrement de l'utilisateur s'il n'y a pas d'erreurs
    // ________________________________________________________
    
    if (count($erreurs) === 0) {

        $champs['utilisateur_type']     = "U";
        $champs['utilisateur_nom']      = $nom;
        $champs['utilisateur_prenom']   = $prenom;
        $champs['utilisateur_courriel'] = $courriel;
        $champs['utilisateur_mdp']      = $mdp;

        $resultat = sqlAjouterLigne($oConn, 'utilisateurs', $champs);
        
        if ( $resultat['errSql'] === "" && $resultat['affected_rows'] === 1) {
            
            // enregistrement effectué correctement -> utilisateur connecté ($_SESSION['utilisateur'] valorisée)
            // _________________________________________________________________________________________________
            
            $champs['utilisateur_id'] = $resultat['insert_id'];
            $_SESSION['utilisateur']  = $champs;

            if (isset($_SESSION['commande'])) {

                // confirmation de la commande en cours éventuelle
                // _______________________________________________
                
                header('Location: confirme-commande.php');
            } else {

                // sinon retour à la page d'index
                // ______________________________
                
                header('Location: index.php'); 
            }
        } else {
            $_SESSION['mesErr']  = $resultat['errSql'] !== "" ? $resultat['errSql'] : "Ajout non effectué";
            header('Location: erreur.php');
        }
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
    <meta name="description" content="Inscription">
    <title>Sport et Plein Air | Inscription</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header>
        <h1><a href="index.php">Sport et Plein Air</a></h1>
    </header>
    <main>
        <h2>Inscription</h2>
        
        <form id="inscription" action="inscription.php" method="post">
            <label>Nom</label>
            <input type="text"     name="nom"      value="<?= isset($nom)      ? $nom      : "" ?>" required>
            <span><?= isset($erreurs['nom'])      ? $erreurs['nom']      : "&nbsp;" ?></span>
            
            <label>Prénom</label>
            <input type="text"     name="prenom"   value="<?= isset($prenom)   ? $prenom   : "" ?>" required>
            <span><?= isset($erreurs['prenom'])   ? $erreurs['prenom']   : "&nbsp;" ?></span>

            <label>Courriel</label>
            <input type="email"    name="courriel" value="<?= isset($courriel) ? $courriel : "" ?>" required>
            <span><?= isset($erreurs['courriel']) ? $erreurs['courriel'] : "&nbsp;" ?></span>

            
            <label>Mot de passe</label>
            <input type="password" name="mdp"      value="<?= isset($mdp)      ? $mdp      : "" ?>" required>
            <span><?= isset($erreurs['mdp'])      ? $erreurs['mdp']      : "&nbsp;" ?></span>
            
            <label>Confirmez le mot de passe</label>
            <input type="password" name="mdp2"     value="<?= isset($mdp2)     ? $mdp2     : "" ?>" required>
            <span><?= isset($erreurs['mdp2'])     ? $erreurs['mdp2']     : "&nbsp;" ?></span>
   
            <input type="submit"   name="envoi"    value="Envoyez"> 
        </form>

    </main>
</body>
</html>	
