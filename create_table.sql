/***********INSTRUCTION SQL POUR CREER NOTRE TABLE PERSONNAGES EN BASE DE DONNEES*******************/
CREATE TABLE IF NOT EXISTS `personnages` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `degats` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `experience` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `niveau` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `nbCoups` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `dateDernierCoup` date NOT NULL DEFAULT '1900-01-01',
  `dateDerniereConnexion` date NOT NULL DEFAULT '1900-01-01',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) DEFAULT CHARSET=utf8;