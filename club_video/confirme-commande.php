<?php

session_start();
require_once("inc/connectDB.php");
require_once("inc/sql.php");

// retour sur la page d'index si l'utilisateur n'est pas connecté ou s'il n'y a pas de commande
// ____________________________________________________________________________________________

if (!isset($_SESSION['utilisateur']) || !isset($_SESSION['commande'])) {
    header('Location: index.php');
    exit;
}

$utilisateur = $_SESSION['utilisateur'];
$commande    = $_SESSION['commande'];

// 1 enregistrement ou pas de la commande en retour du formulaire
// ______________________________________________________________
	
if (isset($_POST['envoi'])) {
    
    // commande confirmée -> enregistrement (transaction)
    // __________________________________________________
    
    if ($_POST['envoi'] === "Confirmez") {

        $resultat = sqlEnregistrerCommande($oConn, $utilisateur, $commande);
        
        unset($_SESSION['commande']);
        if ($resultat['errSql'] !== "") {
            $_SESSION['message'] = $resultat['errSql'];
            if (isset($resultat['errQte'])) {
                $_SESSION['mesQte'] = $resultat['errQte'];
                header('Location: index.php');
            } else {
                header('Location: erreur.php');
            }
            exit;
        } else {
            
            // enregistrement effectué -> affichage d'un message de confirmation sur la page d'index
            // _____________________________________________________________________________________
            
            $_SESSION['message'] = "Votre commande est enregistrée sous le numéro ".$resultat['commande_id'].".";
            header('Location: index.php');
            exit;
        }
    } else {
        
    // commande annulée
    // ________________

        unset($_SESSION['commande']);
        header('Location: index.php');
        exit;
    }
}

// 2 récupération de la liste des produits commandés pour affichage
// ________________________________________________________________

$resultat = sqlConsulterCatalogue($oConn, array_keys($commande));
if ($resultat['errSql'] === "") {
    $liste = $resultat['lignes'];
} else {
    $_SESSION['mesErr'] = $resultat['errSql'];
    header('Location: erreur.php');
    exit;
}

// fin du code PHP au-dessus du code HTML
// ======================================

?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Sport et Plein Air | confirmation de commande</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>

    <header>
        <h1><a href="index.php">Sport et Plein Air</a></h1>
        <ul>
            <li><?= "Bonjour ".$utilisateur['utilisateur_prenom']." ".$utilisateur['utilisateur_nom'] ?></li>
            <li><a href="deconnexion.php">Déconnexion</a></li>
        </ul>
    </header>

    <main id="cat">
        <h2>Confirmation de la commande</h2>
			
		<!-- génération d'un tableau
             avec une ligne par produit commandé, pour confirmation de la commande
             _____________________________________________________________________ -->
			
        <form name="fCat"  action="<?= $_SERVER['PHP_SELF'] ?>" method="post">

            <input type="submit" name="envoi" value="Annulez">
            <input type="submit" name="envoi" value="Confirmez">
            <?php
            
            // boucle principale d'affichage des produits commandés
            // ____________________________________________________
            
            $catRef = "";
            $total = 0;
            foreach ($liste as $row) :
                $produit_id = $row['produit_id'];
                $enStock = $row['produit_stock'] >= $commande[$produit_id] ? true : false;
                if ($enStock) {
                    $totalProduit = $row['produit_prix'] * $commande[$produit_id];
                } else {
                    $_SESSION['commande'][$produit_id] = $row['produit_stock'];
                    $totalProduit = $row['produit_prix'] * $row['produit_stock'];
                }
                $total += $totalProduit;
                if ($row['categorie_nom'] != $catRef) :	// si rupture sur le nom de catégorie
            
                    if ($catRef != "") :
                    ?>
            
            </table>  <!-- fermeture de la table de la catégorie précédente si elle existe -->
                    <?php endif ?>
        
            <h3><?= $row['categorie_nom'] ?></h3>	<!-- ligne titre de la catégorie et rappel des titres des colonnes -->
            <hr>
            <table>
                
                    <?php $catRef = $row['categorie_nom']; ?>
                
                <tr>
                    <th>Numéro</th>
                    <th>Libellé</th>
                    <th>Description</th>
                    <th>Marque</th>
                    <th>Prix</th>
                    <th>Qté commandée/stock</th>
                    <th>Total</th>
                </tr>

                <?php endif ?>			

                <!-- affichage d'une ligne de produit
                     ________________________________ -->

                <tr>
                    <td class="num droite"><?= $row['produit_id'] ?></td>
                    <td class="nom       "><?= $row['produit_nom'] ?></td>
                    <td class="des       "><?= $row['produit_desc'] ?></td>
                    <td class="mar       "><?= $row['marque_nom'] ?></td>
                    <td class="pri droite  <?= $enStock ? "enStock" : "pasEnStock" ?>"><?= $row['produit_prix'] ?> $</td>
                    <td class="com droite  <?= $enStock ? "enStock" : "pasEnStock" ?>">
                        <?= !$enStock ? "stock insuffisant<br>" : "" ?>
                        <?= $commande[$produit_id]."/".$row['produit_stock'] ?>
                        <?= !$enStock ? "&nbsp;&nbsp;-> &nbsp;&nbsp;". $row['produit_stock']."/".$row['produit_stock'] : "" ?>

                    </td>
                    <td class="cum droite  <?= $enStock ? "enStock" : "pasEnStock" ?>"><?= $totalProduit ?> $</td>
                </tr>
            <?php endforeach ?>

            </table>	<!-- fermeture de la table de la dernière catégorie -->
            <p class="tot">Total&nbsp;&nbsp; <?= $total ?> $</p>    
            <input type="submit" name="envoi" value="Annulez">
            <input type="submit" name="envoi" value="Confirmez">
        </form>
    </main>
    
</body>
</html>