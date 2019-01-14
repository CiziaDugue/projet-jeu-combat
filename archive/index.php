/*test git*/

<?php
function chargerClasse($classe) {

	require $classe . '.php'; // On inclut la classe correspondante au paramètre passé.
}

// Lorsqu'on appelle une classe non déclarée, PHP parcourt la pile d'autoload pour la rechercher
// On enregistre ici la fonction en autoload pour qu'elle soit appelée dès qu'on instanciera une classe non déclarée
spl_autoload_register('chargerClasse');

$perso = new Personnage;
