<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 09/05/15
 * Time: 01:39
 */

    namespace Demeter\Model;

    class ApiKey extends \Demeter\Core\DatabaseObject {

        static protected $_DB_CONFIG = Array(
            'table'     => "apikey",
            'prefix'    => "apk_"
        );

        static protected $_DB_FIELDS = Array(
            'user',
            'name',
            'keyId',
            'vCode',
            'status'
        );

        static public $_STATUS = Array(
            'pending',  // just submit but no setup on the apikey
            'ok'        // apikey is setup and ready to use
        );

        static public function createdPendingOne($details) {
            $details    = array_merge($details, Array('status' => 'pending'));
            $response   = self::create($details);
            return \Demeter\Core\Database::getInstance()->lastInsertId();
        }

        static public function getAllForUser($userid) {
            return self::getAll(Array(
                'user'      => $userid
            ));
        }

        static public function getAllLabelsForUser($userid) {
            $apiKeys    = self::getAllForUser($userid);
            $data       = Array();

            foreach($apiKeys as $apiKey) {
                $data[$apiKey['id']] = $apiKey['name'];
            }
            return $data;
        }

        public static function collectData($characterData, $pheal) {
            //////////
            // generic data
            $generic = Array(
                'id'    => $characterData->characterID,
                'name'  => $characterData->name,
            );

            if($characterData->corporationID != 0) {
                $generic['corp'] = Array(
                    'id'    => $characterData->corporationID,
                    'name'  => $characterData->corporationName
                );
            }
            if($characterData->allianceID != 0) {
                $generic['alliance'] = Array(
                    'id'    => $characterData->allianceID,
                    'name'  => $characterData->allianceName
                );
            }

            //////////
            // skills
            $pheal->scope   = 'char';
            $characterSheet = $pheal->CharacterSheet(array("characterID" => $characterData->characterID));
            $skills         = Array();
            foreach($characterSheet->skills as $learnedSkill) {
                $skillID    = (int) $learnedSkill->typeID;
                if(in_array($skillID, array_keys(\Demeter\Model\Character::$_SKILLS))) {
                    $skills[$skillID]   = Array(
                        'id'    => $skillID,
                        'level' => $learnedSkill->level,
                        'name'  => \Demeter\Model\Character::$_SKILLS[$skillID]
                    );
                }
            }

            return Array(
                'generic'   => $generic,
                'skills'    => $skills
            );
        }

    }