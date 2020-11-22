<?php
    
/**
 * Fonction sqlControlerUtilisateur
 * Auteur : Zhang lie
 * Date   : 
 * But    : contrôler l'authentification de l'utilisateur dans la table utilisateurs
 * Arguments en entrée : $oConn = contexte de connexion
 *                       $identifiant
 *                       $mot_de_passe
 * Valeurs de retour   : $resultat['errSql']       = message d'erreur SQL ou chaine vide
 *                       $resultat['utilisateur']  = ligne correspondant à l'utilisateur
 *                                                   tableau vide si non trouvée  
 */
function sqlControlerUtilisateur($oConn, $courriel, $mdp) {

    $req = "SELECT * FROM utilisateurs
            WHERE utilisateur_courriel=? AND utilisateur_mdp = SHA2(?, 256)";

    if($oStmt = $oConn->prepare($req)) {
        if($oStmt->bind_param("ss", $courriel, $mdp)) {
            if ($oStmt->execute()) {
                $oResult = $oStmt->get_result();
                $resultat['errSql'] = "";
                $resultat['utilisateur'] = [];
                if ($oResult->num_rows === 1) {
                    $resultat['utilisateur'] = $oResult->fetch_array(MYSQLI_ASSOC);
                }
                return $resultat;
            }
        }
    }
    $resultat['errSql'] = $oConn->errno." – ".$oConn->error;
    return $resultat;
}

/** Fonction sqlConsulterCatalogue
 * Auteur : Zhang lie
 * Date   : 
 * But    : afficher le catalogue des produits trié par catégorie
 *          en écartant les produits dont le stock est nul
 *          ou bien la liste des produits d'une commande si $produits non vide
 * Arguments en entrée : $oConn    = contexte de connexion
 *                       $produits = tableau optionnel des numéros de produits commandés	 
 * Valeurs de retour   : $resultat['errSql'] = message d'erreur SQL ou chaine vide
 *						 $resultat['lignes'] = lignes des produits du catalogue
 */

function sqlConsulterCatalogue($oConn, $produits = []) {

	// requête de consultation du catalogue
	// ____________________________________
	
	$req = "SELECT P.produit_id, P.produit_nom, P.produit_desc, P.produit_prix, P.produit_stock,
                   M.marque_nom, C.categorie_nom, C.categorie_id
			FROM produits AS P
            INNER JOIN marques    AS M ON P.produit_fk_marque_id = M.marque_id
            INNER JOIN categories AS C ON P.produit_fk_categorie_id = C.categorie_id           	   	
			WHERE";
    
    if (count($produits) === 0) {
        $req .= " P.produit_stock > 0";
    } else {
        $req .= " P.produit_id IN (".implode(",", $produits).")";
    }
            
	$req .= " ORDER BY C.categorie_nom, P.produit_nom ASC";
	
    if ($oResult = $oConn->query($req, MYSQLI_STORE_RESULT)) {					  				                            				       
        $nbResult = $oResult->num_rows;
        $liste = array();
        if ($nbResult) {
            $oResult->data_seek(0);
            while ($row = $oResult->fetch_array(MYSQLI_ASSOC)) {
                $liste[] = $row;
            }
        }
        $oResult->free_result();
        $resultat['errSql'] = "";
        $resultat['lignes'] = $liste;
        return $resultat;
    }
    $resultat['errSql'] = $oConn->errno." – ".$oConn->error;
    return $resultat;
}

/**
 * Fonction sqlLireTable
 * Auteur : Zhang lie
 * Date   : 
 * But    : Récupérer les lignes d'une table sur la condition champ = valeur (condition optionnelle)
 * Arguments en entrée : $oConn  = objet contexte de connexion
 *                       $table  = nom de la table
 *                       $champ  = nom du champ de la condition WHERE
 *                       $valeur = valeur du champ de la condition WHERE      
 * Valeurs de retour   : $resultat['errSql'] = message d'erreur SQL ou chaine vide
 *                       $resultat['lignes'] = lignes correspondant à la condition
 *                                             tableau vide si non trouvées     
 */
function sqlLireTable($oConn, $table, $champ = "", $valeur= "") {
    $req = "SELECT * FROM $table";
    if ($champ !== "") {
        $req .= " WHERE $champ=?";
    }
    if ($oStmt = $oConn->prepare($req)) {
        if ($champ !== "") {
            $oStmt->bind_param("s", $valeur); 
        }
        if ($oStmt->execute()) {
            $oResult = $oStmt->get_result();
            $nbResult = $oResult->num_rows;
            $liste = array();
            if ($nbResult > 0) {
                while ($row = $oResult->fetch_array(MYSQLI_ASSOC)) {
                    $liste[] = $row;
                }
            }
            $oResult->free_result();
            $resultat['errSql'] = "";
            $resultat['lignes']  = $liste;
            return $resultat;
        }
    }
    $resultat['errSql'] = $oConn->errno." – ".$oConn->error;
    return $resultat; 
}

/** 
 * Fonction sqlAjouterLigne
 * Auteur : Zhang lie
 * Date   : 
 * But    : ajouter une ligne dans une table  
 * Arguments en entrée : $oConn  = objet contexte de connexion
 *                       $table  = nom de la table
 *                       $champs = tableau associatif des champs
 * Valeurs de retour   : $resultat['errSql']        = message d'erreur SQL ou chaine vide
 *                       $resultat['affected_rows'] = nbre de lignes ajoutées
 *                       $resultat['insert_id']     = numéro dernier insert_id
 */
function sqlAjouterLigne($oConn, $table, $champs) {
    $req = "INSERT INTO $table SET";
    $types = "";
    foreach ($champs as $cle => $valeur) {
        $types .="s";
        $req   .= " $cle=".($cle === "utilisateur_mdp" ? "SHA2(?, 256)," : "?,"); 
    }
    $req = rtrim($req, ",");
    if ($oStmt = $oConn->prepare($req)) {
        
        // si >= PHP 5.6, opérateur de décomposition "...",
        // pour passer un nombre variable de paramètres à la fonction, ici bind_param
        // __________________________________________________________________________
        
        if ($oStmt->bind_param($types, ...array_values($champs))) { 

            if ($oStmt->execute()) {
                $resultat['errSql']        = "";
                $resultat['affected_rows'] = $oStmt->affected_rows;
                $resultat['insert_id']     = $oConn->insert_id;
                return $resultat;
            }
        }
    }
    $resultat['errSql'] = $oConn->errno." – ".$oConn->error;
    return $resultat; 
}

/** 
 * Fonction sqlEnregistrerCommande
 * Auteur : Zhang lie
 * Date   : 
 * But    : enregistrer la commande d'un utilisateur en mode transactionnel 
 * Arguments en entrée : $oConn       = objet contexte de connexion
 *                       $utilisateur = champs de l'utilisateur connecté
 *                       $produits    = produits et quantités commandées
 * Valeurs de retour   : $resultat['errSql']      = message d'erreur SQL ou chaine vide
 *                       $resultat['errQte']      = booléen à TRUE si la quantité n'est plus disponible 
 *                       $resultat['commande_id'] = numéro de commande  
 */
function sqlEnregistrerCommande($oConn, $utilisateur, $produits) {
    
    if (!$oConn->begin_transaction()) {
        $resultat['errSql'] = $oConn->errno." – ".$oConn->error;
        return $resultat; 
    }
        
    $req = "SELECT * FROM produits WHERE produit_id IN (".implode(",", $produits).") FOR UPDATE";
    if (!$oResult = $oConn->query($req, MYSQLI_STORE_RESULT)) {
        $resultat['errSql'] = $oConn->errno." – ".$oConn->error;
        $oConn->rollback();
        return $resultat;        
    }
    
    $champs['commande_date']              = date("Y-m-d H:i:s");
    $champs['commande_fk_utilisateur_id'] = $utilisateur['utilisateur_id'];
    $resultat = sqlAjouterLigne($oConn, "commandes", $champs);
    if ($resultat['errSql'] !== "" || $resultat['affected_rows'] !== 1) {
        $resultat['errSql'] !== "" ? $resultat['errSql'] : "Ajout dans la table commandes non effectué.";
        $oConn->rollback();
        return $resultat;        
    }
    
    $champs = [];
    $champs['ligne_fk_commande_id'] = $resultat['insert_id'];
    $req = "UPDATE produits SET produit_stock=produit_stock-? WHERE produit_id=? AND produit_stock - ? >= 0";
    if (!$oStmt = $oConn->prepare($req)) {
        $resultat['errSql'] = $oConn->errno." – ".$oConn->error;
        $oConn->rollback();
        return $resultat;     
    }
        
    foreach ($produits as $produit_id => $qte) {
        $champs['ligne_fk_produit_id'] = $produit_id;
        $champs['ligne_qte']           = $qte;

        if ($qte > 0) {
            $resultat = sqlAjouterLigne($oConn, "lignes", $champs);
            if ($resultat['errSql'] !== "" || $resultat['affected_rows'] !== 1) {
                $resultat['errSql'] !== "" ? $resultat['errSql'] : "Ajout dans la table lignes non effectué.";
                $oConn->rollback();
                return $resultat;        
            }

            if (!$oStmt->bind_param("sss", $qte, $produit_id, $qte)) {
                $resultat['errSql'] = "Erreur bind_param sur la requête UPDATE produits";
                $oConn->rollback();
                return $resultat;     
            }

            if (!$oStmt->execute()) {
                $resultat['errSql'] = $oStmt->errno." – ".$oStmt->error;
                $oConn->rollback();
                return $resultat;     
            }

            if ($oStmt->affected_rows !== 1) {
                $resultat['errQte'] = TRUE;
                $resultat['errSql'] = "Commande non enregistrée, la quantité $qte du produit $produit_id n'est plus disponible.";
                $oConn->rollback();
                return $resultat;
            }
        }
    }
    
    $oConn->commit();
    $resultat['errSql'] = "";
    $resultat['commande_id'] = $champs['ligne_fk_commande_id'];
    return $resultat; 
}
?>