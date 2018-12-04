<?php
/**************CLASSE POUR INSTANCIER DES OBJETS PERSONNAGES**************/
// Elle définit les caractéristiques et les fonctionnalités des objets instanciés
class Personnage
{
    private $_id;
    private $_nom;
    private $_degats;
    private $_experience;
    private $_niveau;
    private $_nbCoups;
    private $_dateDernierCoup;
    private $_dateDerniereConnexion;
     
    const CEST_MOI = 1;
    const PERSONNAGE_TUE = 2;
    const PERSONNAGE_FRAPPE = 3;
    const PAS_AUJOURDHUI = 4;
     
    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }
     
 
    public function frapper(Personnage $perso)
    {
        if ($this->id() == $perso->id()){
            return self::CEST_MOI;
        }
         
        $now = new DateTime('NOW');
        $diff = $this->dateDernierCoup()->diff($now);
         
        if ($this->nbCoups() >= 3 && $diff->h + 24*$diff->d < 24) {
            return self::PAS_AUJOURDHUI;
        }
         
        if ($diff->h + 24*$diff->d < 24){
            $this->setNbCoups($this->nbCoups() + 1);
        } else {
            $this->setNbCoups(1);
        }
         
         
        $this->setDateDernierCoup($now->format('Y-m-d'));
         
        return $perso->recevoirDegats($this->niveau() * 5);
    }
 
    public function recevoirDegats($force)
    {
        $this->setDegats($this->degats() + $force);      
        if ($this->degats() >= 100){
            return self::PERSONNAGE_TUE;
        }
        return self::PERSONNAGE_FRAPPE;
    }
     
    public function gagnerExperience(){
        $this->setExperience($this->experience() + $this->niveau() * 5);
         
        if ($this->experience() >= 100){
            $this->setNiveau($this->niveau() + 1);
            $this->setExperience(0);
        }
    }
     
    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value)
        {
            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method))
            {
                $this->$method($value);
            }
        }
    }
     
    public function id()
    {
        return $this->_id;
    }
     
    public function nom()
    {
        return $this->_nom;
    }
     
    public function degats()
    {
        return $this->_degats;
    }
     
    public function experience(){
        return $this->_experience;
    }
     
    public function niveau()
    {
        return $this->_niveau;
    }
     
    public function nbCoups()
    {
        return $this->_nbCoups;
    }
     
    public function dateDernierCoup()
    {
        return $this->_dateDernierCoup;
    }
     
    public function dateDerniereConnexion()
    {
        return $this->_dateDerniereConnexion;
    }
     
    public function setId($id)
    {
        $id = (int) $id;
        if ($id >= 0) {
            $this->_id = $id;
        }
    }
     
    public function setNom($nom)
    {
        if (is_string($nom)) {
            $this->_nom = $nom;
        }
    }
     
    public function setDegats($degats)
    {
        $degats = (int) $degats;
        //if ($degats >= 0 && $degats <= 100) {
            $this->_degats = $degats;
        //}
    }
     
    public function setExperience($experience)
    {
        $experience = (int) $experience;
        //if ($experience >= 0 && $experience <= 100) {
            $this->_experience = $experience;
        //}
    }
     
    public function setNiveau($niveau)
    {
        $niveau = (int) $niveau;
        if ($niveau >= 0 && $niveau <= 100) {
            $this->_niveau = $niveau;
        }
    }
     
    public function setNbCoups($nbCoups)
    {
        $nbCoups = (int) $nbCoups;
        if ($nbCoups >= 0 && $nbCoups <= 100) {
            $this->_nbCoups = $nbCoups;
        }
    }
     
    public function setDateDernierCoup($dateDernierCoup)
    {
        $dateDernierCoup = DateTime::createFromFormat("Y-m-d", $dateDernierCoup);
        $this->_dateDernierCoup = $dateDernierCoup;
    }
     
    public function setDateDerniereConnexion($dateDerniereConnexion)
    {
        $dateDerniereConnexion = DateTime::createFromFormat("Y-m-d", $dateDerniereConnexion);
        $this->_dateDerniereConnexion = $dateDerniereConnexion;
    }
     
    public function nomValide()
    {
        return !(empty($this->_nom));
    }
}