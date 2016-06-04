-- user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `usr_id` int(64) NOT NULL AUTO_INCREMENT,
  `usr_type` ENUM('email', 'eveo') NOT NULL DEFAULT 'email',
  `usr_identifier` TEXT NOT NULL,
  `usr_password` TEXT NOT NULL,
  `usr_created` int(64) NOT NULL,
  `usr_lastConnection` int(64) NOT NULL,
  `usr_lastRequestedUpdate` int(64) NOT NULL,
  `usr_isAdmin` ENUM('Y', 'N') NOT NULL DEFAULT 'N',

  PRIMARY KEY (`usr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- character
DROP TABLE IF EXISTS `character`;
CREATE TABLE IF NOT EXISTS `character` (
  `char_id` int(64) NOT NULL AUTO_INCREMENT,
  `char_apikey` int(64) NOT NULL,
  `char_isActive` enum('Y', 'N') NOT NULL,
  `char_charid` int(64) NOT NULL,
  `char_informations` TEXT NOT NULL,
  `char_skills` TEXT NOT NULL,
  `char_planets` TEXT NOT NULL,
  `char_hasRequested` enum('Y', 'N') NOT NULL,
  `char_created` int(64) NOT NULL,
  `char_lastUpdate` int(64) NOT NULL,
  `char_lastUpdatePlanets` int(64) NOT NULL,

  PRIMARY KEY (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- requestQueue
DROP TABLE IF EXISTS `requestQueue`;
CREATE TABLE IF NOT EXISTS `requestQueue` (
  `rq_id` int(64) NOT NULL AUTO_INCREMENT,
  `rq_userid` int(64) NOT NULL,
  `rq_charid` int(64) NOT NULL,
  `rq_time` int(64) NOT NULL,
  `rq_status` enum('wait', 'inprogress', 'done') DEFAULT 'wait',

  PRIMARY KEY (`rq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- crestCharacter
DROP TABLE IF EXISTS `crestCharacter`;
CREATE TABLE `crestCharacter` (
  `cc_id` int(11) NOT NULL AUTO_INCREMENT,
  `cc_user` int(11) NOT NULL,
  `cc_characterId` int(11) NOT NULL,
  `cc_characterName` text NOT NULL,
  `cc_accessToken` text NOT NULL,
  `cc_status` ENUM('ok','failed','deleted') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`cc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

