<?php
/********CLASSE POUR GERER NOS OBJETS PERSONNAGE********/
// Elle gère les personnages instanciés : elle les inscrit, modifie ou efface dans la base de données
class PersonnagesManager
{
    private $_db; // Instance de PDO
     
    public function __construct($db)
    {
        $this->setDb($db);
    }
    
	// Préparation de la requête d'insertion
    // Assignation des valeurs pour le nom, la force, les dégâts, l'expérience et le niveau du personnage
    // Exécution de la requête
    public function add(Personnage $perso)
    {
        $q = $this->_db->prepare('INSERT INTO personnages (nom, dateDerniereConnexion) VALUES (:nom, NOW())');
        $q->bindValue(':nom', $perso->nom());
        $q->execute();
         
        $now = new DateTime('NOW');
         
        $perso->hydrate([
            'id'=>$this->_db->lastInsertId(),
            'degats' => 0,
            'experience' => 0,
            'niveau' => 1,
            'nbCoups' => 0,
            'dateDernierCoup' => '0000-00-00',
            'dateDerniereConnexion' => $now->format('Y-m-d')]);
    }
    
	// Exécute une requête de type DELETE
    public function count()
    {
        return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
    }
     
    public function delete(Personnage $perso)
    {
        $this->_db->exec('DELETE FROM personnages WHERE id = '.$perso->id());
    }
     
    public function exists($info)
    {
        if (is_int($info))
        {
            return (bool)$this->_db->query('SELECT COUNT(*) FROM personnages WHERE id = '.$info)->fetchColumn();
        }
         
        $q = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :nom');
        $q -> execute([':nom' => $info]);
         
        return (bool) $q->fetchColumn();
    }
     
    public function get($info)
    {
        if (is_int($info))
        {
            $q = $this->_db->query('SELECT id, nom, degats, experience, niveau, nbCoups, dateDernierCoup, dateDerniereConnexion FROM personnages WHERE id = '.$info);
            $donnees = $q->fetch(PDO::FETCH_ASSOC);
             
            return new Personnage($donnees);
        }
         
        $q = $this -> _db ->prepare('SELECT id, nom, degats, experience, niveau, nbCoups, dateDernierCoup, dateDerniereConnexion FROM personnages WHERE nom = :nom');
        $q->execute([':nom' => $info]);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
         
        return new Personnage($donnees);
    }
     
    public function getList($nom)
    {
        $persos = [];
 
        $q  =  $this->_db->prepare('SELECT id, nom, degats, experience, niveau, nbCoups, dateDernierCoup, dateDerniereConnexion FROM personnages WHERE nom <> :nom ORDER BY nom');
        $q->execute([':nom'=>$nom]);
 
        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            $persos[] = new Personnage($donnees);
        }
        return $persos;
    }
     
    public function update(Personnage $perso)
    {
        $q  =  $this->_db->prepare('UPDATE personnages SET degats = :degats, experience = :experience, niveau = :niveau, nbCoups = :nbCoups, dateDernierCoup = :dateDernierCoup, dateDerniereConnexion = :dateDerniereConnexion WHERE id = :id');
        $q->bindValue(':degats',$perso->degats(), PDO::PARAM_INT);
        $q->bindValue(':experience',$perso->experience(), PDO::PARAM_INT);
        $q->bindValue(':niveau',$perso->niveau(), PDO::PARAM_INT);
        $q->bindValue(':nbCoups',$perso->nbCoups(), PDO::PARAM_INT);
        $q->bindValue(':dateDernierCoup',$perso->dateDernierCoup()->format('Y-m-d'), PDO::PARAM_STR);
        $q->bindValue(':dateDerniereConnexion',$perso->dateDerniereConnexion()->format('Y-m-d'), PDO::PARAM_STR);
        $q->bindValue(':id',$perso->id(), PDO::PARAM_INT);
        $q->execute();
    }
     
    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
     
}