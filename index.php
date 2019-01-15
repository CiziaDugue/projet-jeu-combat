<?php
/********FICHIER PRINCIPAL -> EXECUTION DU PROGRAMME********/

//Fonction d'autoloading pour les classes Personnage et PersonnageManager
function chargerClasse($classname) {
    require $classname.'.php';
}
 
spl_autoload_register('chargerClasse');

//Ouverture de session
session_start();

//Fermeture de session si la variable get avec la clé deconnexion est définie (au clic sur deconnexion)
if (isset($_GET['deconnexion'])) {
    session_destroy();
    header('Location: .');
    exit();
}

//si on est connecté, on stocke la variable de session avec la clé perso dans une variable avec un nom plus pratique
if (isset($_SESSION['perso'])) {
    $perso = $_SESSION['perso'];
}
 
//Instanciation d'un objet PDO = connexion avec la base de données
$db = new PDO('mysql:host=localhost;dbname=projet_jeu_combat','projet_jeu_combat','rrsf34(42MknE74');
//configuration de l'attribut rapports d'erreur de la classe PDO
//WARNING = erreurs affichées mais n'interrompt pas le code
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

//Instanciation d'un manager
$manager = new PersonnagesManager($db);

//Au clic sur le btn créer, si un nom a été entré par l'utilisateur...
if (isset($_POST['creer']) && isset($_POST['nom'])){
	
	//Instanciation d'un objet personnage avec pour paramètre le nom saisi par l'utilisateur
    $perso = new Personnage(['nom' => $_POST['nom']]);
    
	//Si le nom n'est pas valide...
    if (!$perso->nomValide()){
		//...message d'erreur
        $message = 'Le nom choisi est invalide.';
		//...reset de l'objet perso
    	unset($perso);
    }
	//S'il est déjà présent en bdd...
	elseif ($manager->exists($perso->nom())){
        $message = 'Le nom du personnage est déjà pris.';
        unset($perso);
    }
	//Sinon, tout est ok, ajout du perso en bdd
	else {
        $manager->add($perso);
    }
     
}

//Si un personnage existant est sélectionné
else if (isset($_POST['utiliser']) && isset($_POST['nom'])){
	//on vérifie s'il existe
    if ($manager->exists($_POST['nom']))
    {
		//méthode get du manager
        $perso = $manager->get($_POST['nom']);
        
		//Modif dernière date de connexion
        $now = new DateTime('NOW');
        $diff = $perso->dateDerniereConnexion()->diff($now);
        
		//Si delai depuis dernière connexion > 24h
        if ($diff->h + 24*$diff->d > 24){
            $perso->setDateDerniereConnexion($now->format('Y-m-d'));
			//et que les dégats du perso sont >=10
            if ($perso->degats() >= 10) {
				//diminution des dégats de 10 points
                $perso->setDegats($perso->degats() - 10);
            }
			//et mise à jour des données du perso
            $manager->update($perso);
        }
         
    } else {
		//Si le personnage n'existe pas
        $message = 'Ce personnage n\'existe pas !';
    }
//Si l'utilisateur clique sur un adversaire pour l'attaquer    
} elseif (isset($_GET['frapper'])){
    
	//Si il n'est pas connecté, message d'erreur
    if (!isset($perso)){
        $message = 'Merci de créer un personnage ou de vous identifier.';
    } else {
		//Si le personnage que l'on veut frapper n'est plus en bdd
        if (!$manager->exists((int) $_GET['frapper'])){
            $message = 'Le personnage que vous voulez frapper n\'existe pas!';
        } else {
            //S'il existe, on récupère ses données avec le manager et on crée un objet persoafrapper
            $persoAFrapper = $manager->get((int) $_GET['frapper']);
            $retour = $perso->frapper($persoAFrapper);
            
            switch($retour)
            {
				//Si l'utilisateur clique sur son propre personnage, message & break
                case Personnage::CEST_MOI :
                    $message = 'Mais... pouquoi voulez-vous vous frapper ???';
                    break;
				//Si l'utilisateur a déjà attaqué 3 fois ce personnage aujourd'hui, message & break
                case Personnage::PAS_AUJOURDHUI :
                    $message = 'Vous avais déjà frappé 3 fois aujourd\'hui. Revenez demain !';
                    break;
				//Si l'adversaire peut encore être frappé, message
                case Personnage::PERSONNAGE_FRAPPE :
                    $message = 'Le personnage a bien été frappé !';
                    //Méthode setter gain d'XP
                    $perso->gagnerExperience();
                    //Update des profils des deux personnages
                    $manager->update($perso);
                    $manager->update($persoAFrapper);
                     
                    break;
				//Si l'adversaire meurt, message
                case Personnage::PERSONNAGE_TUE;
                    $message = 'Vous avez tué ce personnage !';
                    //Méthode setter gain d'XP
                    $perso->gagnerExperience();
 					//Update du profil du perso connecté et suppression de celui de l'adversaire
                    $manager->update($perso);
                    $manager->delete($persoAFrapper);
                     
                    break;
            }
        }
    }
}
 
?>
<!DOCTYPE html>
<html>
    <head>
        <title>TP : Mini jeu de combat</title>
        <meta charset="utf-8" />
    </head>
    <body>
     
        <p> Nombre de personnages créés : <?= $manager->count() ?></p>
    <?php
		//Affichage des messages
        if (isset($message)){
            echo '<p>'. $message . '</p>';
        }
        
		//Si un perso est connecté, affichage btn déconnexion, champ avec les infos du personnage, champ avec les adversaires
        if (isset($perso)){
        ?>
            <p><a href="?deconnexion=1">Déconnexion</a></p>
         
            <fieldset>
                <legend>Mes informations</legend>
                <p>
                    Nom : <?=  htmlspecialchars($perso->nom()) ?><br><br>
                    Dégâts : <?= $perso->degats() ?> | 
                    Expérience : <?= $perso->experience() ?> | 
                    Niveau : <?= $perso->niveau() ?> | 
                    Nombre des coups : <?= $perso->nbCoups() ?> | 
                    Date de dernier coup : <?= $perso->dateDernierCoup()->format('d/m/Y') ?> | 
                    Dernière connexion : <?= $perso->dateDerniereConnexion()->format('d/m/Y') ?>
                </p>
            </fieldset>
            <fieldset>
                <legend>Qui frapper?</legend>
                <p>
                    <?php
                    //Méthode pour récupérer la liste des adversaires avec leurs stats
                    $persos = $manager->getList($perso->nom());
					//Si il n'y a pas d'autres persos
                    if (empty($persos)) {
                        echo 'Personne à frapper!';
                    } else {
                        foreach($persos as $unPerso){
                            echo '<a href="?frapper='.$unPerso->id().'">'.htmlspecialchars($unPerso->nom()).'</a> (Dégâts : '.$unPerso->degats().' | Expérience : '.$unPerso->experience().' | Niveau : '.$unPerso->niveau().' | Nombre des coups : '.$unPerso->nbCoups().' | Date de dernier coup : '.$unPerso->dateDernierCoup()->format('d/m/Y').' | Dernière connexion : '.$unPerso->dateDerniereConnexion()->format('d/m/Y').')<br><br>';
                             
                        }
                    }
                     
                    ?>
                </p>
            </fieldset>
             
         
        <?php
 
        } else {
             
    ?>
            <form action="" method = "post">
                <p>
                    Nom : <input type="text" name="nom" maxlength="50" />
                    <input type="submit" value = "Créer ce personnage" name="creer" />
                    <input type="submit" value = "Utiliser ce personnage" name="utiliser" />
                </p>
            </form>
    <?php
        }
    ?>
     
     
     
    </body>
</html>
<?php
if (isset($perso)){
    $_SESSION['perso'] = $perso;
}
?>