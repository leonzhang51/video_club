// affichage sélectif du catalogue par catégorie
// _____________________________________________	

window.addEventListener("load", pageLoaded);

function pageLoaded() {
    
    var sections = document.getElementsByTagName("section");

    // fonction activée lorsque l'on clique sur un choix de catégorie de la balise select
    // __________________________________________________________________________________

    document.getElementById("categories").addEventListener("change", function () {

        var choix = this.value;
        
        if (choix === "cat-toutes") {
           
            // choix toutes les catégories -> les afficher en supprimant la classe "cache" sur toutes les sections correcpondantes
            // ___________________________________________________________________________________________________________________
            
            for (var i= 0; i < sections.length; i++) {
                sections[i].classList.remove("cache"); 
            } 
        
        } else {
            
            // choix d'une catégorie -> cacher toutes les catégories puis afficher celle sélectionnée
            // ______________________________________________________________________________________
            
            for (var i= 0; i < sections.length; i++) {
                sections[i].classList.add("cache"); 
            }
            
            document.getElementById(choix).classList.remove("cache"); // affichage en supprimant la classe cache
        }
    });
}
