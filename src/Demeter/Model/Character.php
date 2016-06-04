<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 20/05/15
 * Time: 12:41
 */

    namespace Demeter\Model;

    class Character extends \Demeter\Core\DatabaseObject {

        static protected $_DB_CONFIG = Array(
            'table'     => "character",
            'prefix'    => "char_"
        );

        static protected $_DB_FIELDS = Array(
            'apikey',
            'charid',
            'isActive',         // 'Y'/'N'
            'informations',     // contains basic informations about the character  - php array serialized
            'skills',           // contains skills that are related to PI           - php array serialized
            'planets',          // contains all planets data, fetch by request      - php array serialized
            'hasRequested',     // if the character has requested or not an update  - 'Y'/'N'
            'created',          // timestamp
            'lastUpdate',       // timestamp
            'lastUpdatePlanets' // timestamp
        );

        static public $_SKILLS = Array(
            2495    => 'Interplanetary Consolidation',  // number of planets
            2505    => 'Command Center Upgrades',       // command center level (+1)
            2406    => 'Planetology',                   // ability to find ressources on planets
            2403    => 'Advanced Planetology',          // ability to find more ressources on planets
            13279   => 'Remote Sensing'                 // ability to remotely control your planets
        );

        static public function getForApiKey($apiKey) {
            return self::getAll(Array(
                'apikey'    => $apiKey
            ));
        }

        static public function getActivesForUser($userid) {
            $apiKeys    = \Demeter\Model\ApiKey::getAllForUser($userid);
            $chars      = Array();

            foreach($apiKeys as $apiKey) {
                $data   = Array();
                $raw    = self::getAll(Array(
                    'apikey'    => $apiKey['id'],
                    'isActive'  => 'Y'
                ));

                foreach($raw as $char) {
                    $char['informations']   = unserialize($char['informations']);
                    $char['skills']         = unserialize($char['skills']);
                    $char['planets']        = unserialize($char['planets']);

                    if(isset($char['planets']) && is_array($char['planets'])) {
                        foreach($char['planets'] as $planetId => $planet) {
                            $elapsedArray = Array();
                            foreach($planet['extractors'] as $extractorId => $extractor) {
                                $elapsed = round((1 - ((time() - $extractor['expiry']) / ($extractor['install'] - $extractor['expiry']))) * 100, 0);
                                if($elapsed > 100) {
                                    $elapsed = 100;
                                }

                                $elapsedArray[]                                                     = $elapsed;
                                $char['planets'][$planetId]['extractors'][$extractorId]['elapsed']  = $elapsed;
                            }

                            if(count($elapsedArray) > 0) {
                                $char['planets'][$planetId]['elapsed'] = array_sum($elapsedArray) / count($elapsedArray);
                            } else {
                                $char['planets'][$planetId]['elapsed'] = 100;
                            }
                        }
                    } else {
                        $char['planets']    = Array();
                    }
                    $data[] = $char;
                }

                $chars = array_merge($chars, $data);
            }

            return $chars;
        }
    }