<?php

session_start();
require_once("inc/connectDB.php");
require_once("inc/sql.php");

unset($_SESSION['admin']);
unset($_SESSION['commande']);
$utilisateur = isset($_SESSION['utilisateur']) ? $_SESSION['utilisateur'] : "";
$message = isset($_SESSION['message']) ? $_SESSION['message'] : "&nbsp;";
$mesQte  = isset($_SESSION['mesQte'])  ? $_SESSION['mesQte'] : FALSE;
unset($_SESSION['message']);
unset($_SESSION['mesQte']);

// 1 valorisation de la variable de session "commande" en retour du formulaire
// ___________________________________________________________________________
	
$errMes = "&nbsp;";
if (isset($_POST['envoi'])) { 
    
    // il y a eu au moins un produit sélectionné (case à cocher)
    // _________________________________________________________
    
	if (isset($_POST['choix'])) {
        $errQte = false;
        
        // vérification si une quantité a été sélectionnée pour chaque produit coché
        // _________________________________________________________________________
        
        foreach($_POST['choix'] as $produit) { 	
            
            // en testant l'existence d'une variable "<numero de produit>" 
            // ___________________________________________________________
            
            if ($_POST[$produit] !== "") $commande[$produit] = $_POST[$produit];
            else $errQte = true;
        }
        
        // si au moins un des produit n'a pas de quantité
        // ______________________________________________
        
        if ($errQte == true) {               	 
            $errMes = "Vous avez sélectionné des produits sans préciser la quantité.";
        } else {
            
        // sinon, mémorisation de la commande
        // __________________________________
            
            $_SESSION['commande']  = $commande;
            
            if ($utilisateur !== "") {
                // confirmation de la commande si l'utilisateur est connecté
                header('Location: confirme-commande.php');
            } else {
                // connexion préalable de l'utilisateur avant de confirmer sa commande
                header('Location: authentification.php');
            }
            exit;
        }	
	} else {
    
    // sinon message d'information
    // ___________________________
        
        $errMes = "Vous n'avez sélectionné aucun article.";  
	}
}

// 2 récupération de la liste des produits
// _______________________________________
	
$resultat = sqlConsulterCatalogue($oConn);
if ($resultat['errSql'] === "") {
    $liste = $resultat['lignes'];
} else {
    $_SESSION['mesErr'] = $resultat['errSql'];
    header('Location: erreur.php');
    exit;
}

// 3 récupération de la liste des catégories
// _________________________________________
	
$resultat = sqlLireTable($oConn, "categories");
if ($resultat['errSql'] === "") {
    $categories = $resultat['lignes'];
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
	<title>Sport et Plein Air</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script src="js/categories.js"></script>
</head>

<body>

    <header>
        <h1><a href="index.php">Sport et Plein Air</a></h1>
        <ul>
            <li><?= $utilisateur !== "" ? "Bonjour ".$utilisateur['utilisateur_prenom']." ".$utilisateur['utilisateur_nom']: ""?></li>
            <li><a href="<?= $utilisateur !== "" ? "deconnexion.php" : "authentification.php" ?>"><?= $utilisateur !== "" ? "Déconnexion" : "Connexion" ?></a></li>
            
            <?php if ($utilisateur === "") : ?>

            <li><a href="inscription.php">Inscription</a></li>
            <?php endif; ?>

        </ul>
    </header>

    <main id="cat">
        <header>
            <h2>Notre catalogue</h2>
            <select id="categories">
                <option value="cat-toutes">Toutes les catégories</option>
            
                <?php foreach ($categories as $categorie) : ?>
            
                <option value="cat-<?= $categorie['categorie_id'] ?>"><?= $categorie['categorie_nom'] ?></option>
            
                <?php endforeach; ?>
            
            </select>
        </header>
        <p class="mes <?= $mesQte ? "qte" : ""?>"><?= $message ?></p>
        
		<!-- génération d'un tableau formulaire
             avec une ligne par produit et une case à cocher de sélection dans la dernière cellule
             _____________________________________________________________________________________ -->
			
        <form name="fCat"  action="<?= $_SERVER['PHP_SELF'] ?>" method="post">

            <span><?= $errMes ?></span> 

            <input type="submit" name="envoi" value="Commandez les articles sélectionnés">
            
        <?php
        
        // boucle principale d'affichage des produits
        // __________________________________________
            
        $catRef = "";
        
        foreach ($liste as $row) :

            // affichage de l'en-tête d'une nouvelle catégorie avec le premier produit de cette catégorie
            // __________________________________________________________________________________________
            
            if ($row['categorie_nom'] != $catRef) :	// si rupture sur le nom de catégorie
            
                if ($catRef != "") :
                ?>

                </table>
            </section>
                <?php endif ?>

            
    
            <section  id="cat-<?= $row['categorie_id'] ?>">
                <h3><?= $row['categorie_nom'] ?></h3>
                <hr>
                <table>

                <?php $catRef = $row['categorie_nom']; ?>

                    <tr>
                        <th>Numéro</th>
                        <th>Libellé</th>
                        <th>Description</th>
                        <th>Marque</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Sélectionnez</th>
                    </tr>

            <?php endif ?>

                    <!-- affichage d'une ligne de produit
                         ________________________________ -->

                    <tr>
                        <td class="num droite"><?= $row['produit_id'] ?></td>
                        <td class="nom       "><?= $row['produit_nom'] ?></td>
                        <td class="des       "><?= $row['produit_desc'] ?></td>
                        <td class="mar       "><?= $row['marque_nom'] ?></td>
                        <td class="pri droite"><?= $row['produit_prix'] ?> $</td>

                        <td><input class="sel"
                                   type="number"
                                   name="<?= $row['produit_id'] ?>"
                                   min=1
                                   max=<?= $row['produit_stock'] ?> 
                                   value="1"> 
                        </td>
                        <td><input class="sel" type="checkbox" name="choix[]" value="<?= $row['produit_id'] ?>"></td>
                    </tr>
            <?php endforeach ?>

                </table>
            </section>

            <input type="submit" name="envoi" value="Commandez les articles sélectionnés">
        </form>
    </main>
    
</body>
</html>