<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 08/06/15
 * Time: 23:16
 */

/*
CREATE TABLE `invTypes` (
  `typeID` int(11) NOT NULL,
  `groupID` int(11) DEFAULT NULL,
  `typeName` varchar(100) DEFAULT NULL,
  `description` varchar(3000) DEFAULT NULL,
  `mass` double DEFAULT NULL,
  `volume` double DEFAULT NULL,
  `capacity` double DEFAULT NULL,
  `portionSize` int(11) DEFAULT NULL,
  `raceID` tinyint(3) unsigned DEFAULT NULL,
  `basePrice` decimal(19,4) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `marketGroupID` int(11) DEFAULT NULL,
  `chanceOfDuplicating` double DEFAULT NULL,
  PRIMARY KEY (`typeID`),
  KEY `invTypes_IX_Group` (`groupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

    class EveItem extends DatabaseObject {
        static protected $_DB_CONFIG = array(
            'table'     => "invTypes",
            'prefix'    => ""
        );

        static protected $_DB_FIELDS = array(
            'typeID',
            'groupID',
            'typeName',
            'description',
            'mass',
            'volume',
            'capacity',
            'portionSize',
            'raceID',
            'basePrice',
            'published',
            'marketGroupID',
            'chanceOfDuplicating'
        );

        public function __construct($details = array()) {
            $this->hasId = false;
            return parent::__construct($details);
        }
    }