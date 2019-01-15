<?php
/**************CLASSE POUR INSTANCIER DES OBJETS PERSONNAGES**************/
// Elle définit les caractéristiques et les fonctionnalités des objets instanciés
class Personnage
{
	/*Définition des attributs de l'objet*/
	// Ce sont les caractériques du personnage à instancier
	// private = accessible seulement par le code écrit dans la classe = encapsulation
    private $_id;
    private $_nom;
    private $_degats;
    private $_experience;
    private $_niveau;
    private $_nbCoups;
    private $_dateDernierCoup;
    private $_dateDerniereConnexion;
    
	/*Définition de constantes de classe*/
    const CEST_MOI = 1;
    const PERSONNAGE_TUE = 2;
    const PERSONNAGE_FRAPPE = 3;
    const PAS_AUJOURDHUI = 4;
    
	/*Constructeur = méthode pour appliquer des règles à la contruction d'objet*/
	// On veut créer des objets en donnant des valeurs aux attributs déclarés précédemment
	// public = accessible partout, pour pouvoir appeler les méthodes sur l'objet à l'extérieur de la classe / de l'objet ?
	// this désigne l'objet courrant
    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }
     
	/*Méthodes définissant les actions possibles pour les personnages du jeu*/
	//Frapper un personnage
    public function frapper(Personnage $perso)
    {
		//Si le perso essaie de se frapper
        if ($this->id() == $perso->id()){
			//...on renvoie la constante CEST MOI
            return self::CEST_MOI;
        }
        
		//On récupère la date et l'heure de l'attaque
        $now = new DateTime('NOW');
        $diff = $this->dateDernierCoup()->diff($now);
        
		//Si le perso a déjà frappé 3 fois aujourd'hui
        if ($this->nbCoups() >= 3 && $diff->h + 24*$diff->d < 24) {
			//...on renvoie la constante CEST MOI
            return self::PAS_AUJOURDHUI;
        }
        
		//Compte du nombre total de coups portés
        if ($diff->h + 24*$diff->d < 24){
            $this->setNbCoups($this->nbCoups() + 1);
        } else {
            $this->setNbCoups(1);
        }
         
        //Mise à jour de la date du dernier coup
        $this->setDateDernierCoup($now->format('Y-m-d'));
        
		
        return $perso->recevoirDegats($this->niveau() * 5);
    }
	
	//Recevoir des dégats
    public function recevoirDegats($force)
    {
        $this->setDegats($this->degats() + $force);
		
		//Si le perso recoit au moins 100 de dégat, il meurt
        if ($this->degats() >= 100){
			//...on renvoie la constante PERSONNAGE_TUE
            return self::PERSONNAGE_TUE;
        }
		//Sinon on renvoie la constante PERSONNAGE_FRAPPE
        return self::PERSONNAGE_FRAPPE;
    }
    
	//Gagner de l'expérience
    public function gagnerExperience(){
		//A chaque coup, le perso gagne autant de point d'XP que son niveau * 5
        $this->setExperience($this->experience() + $this->niveau() * 5);
        
		//Si XP >= 100, le perso gagne un niveau
        if ($this->experience() >= 100){
            $this->setNiveau($this->niveau() + 1);
            $this->setExperience(0);
        }
    }
    
	/*Méthode pour hydrater les objets = donner des valeurs à leurs propriétés*/
    // Un tableau de données doit être passé à la fonction (d'où le mot-clé array)
	// On veut attribuer des valeurs de la bdd aux propriétés de l'objet avec les setters car ils controlent l'intégrité des valeurs
    public function hydrate(array $donnees)
    {
		// Boucle pour parcourir le tableau $donnees
        foreach ($donnees as $key => $value)
        {
			// Déclaration d'une variable $method avec la même syntaxe que nos setters
			// ucfirst pour passer la 1ere lettre de la clé en MAJ
            $method = 'set'.ucfirst($key);
			
			// Vérification : si la méthode existe
            if (method_exists($this, $method))
            {
				// ...on l'appelle avec la valeur du tableau en argument
                $this->$method($value);
            }
        }
    }
    
	/*Définition des getters*/
	// Méthodes qui retournent la valeur des propriétés de l'objet (this désigne l'objet courrant)
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
    
	/*Définition des setters*/
	// Méthodes pour modifier la valeur des propriétés de l'objet (this désigne l'objet courrant)
	// Vérification de la validité des données
    public function setId($id)
    {
		//On convertit l'argument en nombre entier (ne fait rien si entier, sinon donne 0)
        $id = (int) $id;
		
		//On vérifie s'il est strictement positif
        if ($id >= 0) {
			
			//Si c'est le cas, on assigne la valeur à l'attribut correspondant
            $this->_id = $id;
        }
    }
     
    public function setNom($nom)
    {
		//On vérifie qu'il s'agit bien d'une chaîne de caractères
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
		//Vérification de la validité du nom saisi
        return !(empty($this->_nom));
    }
}