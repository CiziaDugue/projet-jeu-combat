<?php
/********FICHIER PRINCIPAL -> EXECUTION DU PROGRAMME********/
//Autoload des classes Personnage et PersonnageManager
function chargerClasse($classname)
{
    require $classname.'.php';
}
 
spl_autoload_register('chargerClasse');

session_start();
 
if (isset($_GET['deconnexion'])){
    session_destroy();
    header('Location: .');
    exit();
}
 
if (isset($_SESSION['perso'])){
    $perso = $_SESSION['perso'];
}
 
//Instanciation d'un objet PDO
$db = new PDO('mysql:host=localhost;dbname=projet_jeu_combat','projet_jeu_combat','rrsf34(42MknE74');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
 
$manager = new PersonnagesManager($db);
 
if (isset($_POST['creer']) && isset($_POST['nom'])){
    $perso = new Personnage(['nom' => $_POST['nom']]);
    
    if (!$perso->nomValide()){
        $message = 'Le nom choisi est invalide.';
    	unset($perso);
    } elseif ($manager->exists($perso->nom())){
        $message = 'Le nom du personnage est déjà pris.';
        unset($perso);
    } else {
        $manager->add($perso);
    }
     
} elseif (isset($_POST['utiliser']) && isset($_POST['nom'])){
    if ($manager->exists($_POST['nom']))
    {
        $perso = $manager->get($_POST['nom']);
         
        $now = new DateTime('NOW');
        $diff = $perso->dateDerniereConnexion()->diff($now);
         
        if ($diff->h + 24*$diff->d > 24){
            $perso->setDateDerniereConnexion($now->format('Y-m-d'));
            if ($perso->degats() >= 10) {
                $perso->setDegats($perso->degats() - 10);
            }
            $manager->update($perso);
        }
         
    } else {
        $message = 'Ce personnage n\'existe pas !';
    }
     
} elseif (isset($_GET['frapper'])){
     
    if (!isset($perso)){
        $message = 'Merci de créer un personnage ou de vous identifier.';
    } else {
        if (!$manager->exists((int) $_GET['frapper'])){
            $message = 'Le personnage que vous voulez frapper n\'existe pas!';
        } else {
             
            $persoAFrapper = $manager->get((int) $_GET['frapper']);
            $retour = $perso->frapper($persoAFrapper);
             
            switch($retour)
            {
                case Personnage::CEST_MOI :
                    $message = 'Mais... pouquoi voulez-vous vous frapper ???';
                    break;
                case Personnage::PAS_AUJOURDHUI :
                    $message = 'Vous avais déjà frappé 3 fois aujourd\'hui. Revenez demain !';
                    break;
                case Personnage::PERSONNAGE_FRAPPE :
                    $message = 'Le personnage a bien été frappé !';
                     
                    $perso->gagnerExperience();
                     
                    $manager->update($perso);
                    $manager->update($persoAFrapper);
                     
                    break;
                case Personnage::PERSONNAGE_TUE;
                    $message = 'Vous avez tué ce personnage !';
                     
                    $perso->gagnerExperience();
 
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
        if (isset($message)){
            echo '<p>'. $message . '</p>';
        }
         
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
                     
                    $persos = $manager->getList($perso->nom());  
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