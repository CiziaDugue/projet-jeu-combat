<?php
/*Git : modification sur branche Test*/

/*Classe pour gérer nos objets personnages*/
// Elle définit les caractéristiques et les fonctionnalités des objets instanciés

class Personnage {}
?>


<?php

// On admet que $db est un objet PDO.

$request = $db->query('SELECT id, nom, forcePerso, degats, niveau, experience FROM personnages');
    
while ($donnees = $request->fetch(PDO::FETCH_ASSOC)) // Chaque entrée sera récupérée et placée dans un array.

{
  // On passe les données (stockées dans un tableau) concernant le personnage au constructeur de la classe.
  // On admet que le constructeur de la classe appelle chaque setter pour assigner les valeurs qu'on lui a données aux attributs correspondants.

  $perso = new Personnage($donnees);
        
  echo $perso->nom(), ' a ', $perso->forcePerso(), ' de force, ', $perso->degats(), ' de dégâts, ', $perso->experience(), ' d\'expérience et est au niveau ', $perso->niveau();
}
