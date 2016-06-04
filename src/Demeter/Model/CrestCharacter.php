<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 28/04/16
 * Time: 14:03
 */

/*
CREATE TABLE `crestCharacter` (
  `cc_id` int(11) NOT NULL,
  `cc_user` int(11) NOT NULL,
  `cc_name` text NOT NULL,
  `cc_accessToken` text NOT NULL,
  `cc_status` ENUM('ok','deleted') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`cc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

    namespace Demeter\Model;

    class CrestCharacter extends \Demeter\Core\DatabaseObject {

        static protected $_DB_CONFIG = Array(
            'table'     => "crestCharacter",
            'prefix'    => "cc_"
        );

        static protected $_DB_FIELDS = Array(
            'user',         // link to User object
            'characterId',  // characterId
            'characterName',// characterName
            'accessToken',  // CREST token
            'status'        // ok/deleted
        );

    }