<?php
/*Classe pour instancier des objets personnages*/
// Elle définit les caractéristiques et les fonctionnalités des objets instanciés

class Personnage {
	
	/*Définition des attributs de l'objet*/
	// Ce sont les caractériques du personnage à instancier
	// private = accessible seulement par le code écrit dans la classe = encapsulation
	
	private $_id;
	private $_nom;
	private $_forcePerso;
	private $_degats;
	private $_niveau;
	private $_experience;
	
	
	/*Définition de constantes de classe*/
	
	
	/*Constructeur = méthode pour appliquer des règles à la contruction d'objet*/
	// On veut créer des objets en donnant des valeurs aux attributs déclarés précédemment
	// public = accessible partout, pour pouvoir appeler les méthodes sur l'objet à l'extérieur de la classe / de l'objet ?
	// this désigne l'objet courrant
	
	public function __construct(array $donnees) {
		
        $this->hydrate($donnees);
	}
	
    
    /*Méthode pour hydrater les objets = donner des valeurs à leurs propriétés*/
    // Un tableau de données doit être passé à la fonction (d'où le mot-clé array)
	// On veut attribuer des valeurs de la bdd aux propriétés de l'objet avec les setters car ils controlent l'intégrité des valeurs

	public function hydrate(array $donnees) {
		
		// Boucle pour parcourir le tableau $donnees
		foreach ($donnees as $key => $value) {
			
			// Déclaration d'une variable $method avec la même syntaxe que nos setters
			// ucfirst pour passer la 1ere lettre de la clé en MAJ
			$method = 'set'.ucfirst($key);

			// Vérification : si la méthode existe
			if (method_exists($this, $method)) {
				
				// ...on l'appelle avec la valeur du tableau en argument
				$this->$method($value);
			}
		}
	}

	/*Définition des getters*/
	// Méthodes qui retournent la valeur des propriétés de l'objet (this désigne l'objet courrant)
	
	public function id() {
		return $this->_id;
	}
	public function nom() {
		return $this->_nom;
	}
	public function forcePerso() {
		return $this->_forcePerso;
	}
	public function degats() {
		return $this->_;
	}
	public function niveau() {
		return $this->_niveau;
	}
	public function experience() {
		return $this->_experience;
	}
	
	/*Définition des setters*/
	// Méthodes pour modifier la valeur des propriétés de l'objet (this désigne l'objet courrant)
	// Vérification de la validité des données
	
	public function setId($id) {
		
		//On convertit l'argument en nombre entier (ne fait rien si entier, sinon donne 0)
		
		$id = (int) $id;
		
		//On vérifie s'il est strictement positif
		
		if ($id > 0) {
			
			//Si c'est le cas, on assigne la valeur à l'attribut correspondant
			
			$this->_id = $id;
		}
	}
	
	public function setNom($nom) {
		
		//On vérifie qu'il s'agit bien d'une chaîne de caractères
		
		if (is_string($nom)) {
			
			$this->_nom = $nom;
		}
	}
	
	public function setForcePerso($forcePerso) {
		
		$forcePerso = (int) $forcePerso;
		
		//On vérifie s'il est strictement positif
		
		if ($forcePerso >= 1 && $forcePerso <= 100) {
			
			$this->_forcePerso = $forcePerso;
		}
	}
	
	public function setDegats($degats) {
		
		$degats = (int) $degats;
		
		//On vérifie s'il est strictement positif
		
		if ($degats >= 1 && $degats <= 100) {
			
			$this->_degats = $degats;
		}
	}
	
	public function setNiveau($niveau) {
		
		$niveau = (int) $niveau;
		
		//On vérifie s'il est strictement positif
		
		if ($niveau >= 1 && $niveau <= 100) {
			
			$this->_niveau = $niveau;
		}
	}
	
	public function setExperience($experience) {
		
		$experience = (int) $experience;
		
		//On vérifie s'il est strictement positif
		
		if ($experience >= 1 && $experience <= 100) {
			
			$this->experience = $experience;
		}
	}
}
