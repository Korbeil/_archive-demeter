<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 07/06/15
 * Time: 19:36
 */

    class RequestQueue extends DatabaseObject {
        static protected $_DB_CONFIG = Array(
            'table'     => "requestQueue",
            'prefix'    => "rq_"
        );

        static protected $_DB_FIELDS = Array(
            'userid',
            'charid',
            'time',
            'status'        // 'wait', 'inprogress', 'done'
        );

        public static function add($userid, $charid) {
            $data = Array(
                'userid' => $userid,
                'charid' => $charid,
                'time'   => time(),
                'status' => 'wait'
            );

            $id = self::create($data);
            return array_merge(Array('id' => $id), $data);
        }

        public static function checkForUser($userid) {
            return self::getAll(Array(
                'userid' => $userid,
                'status' => 'wait'
            ));
        }

        public static function checkForCharacter($userid, $charid) {
            return self::getAll(Array(
                'userid' => $userid,
                'charid' => $charid,
                'status' => 'wait'
            ));
        }

        public static function checkForUserInTime($userid, $time = 600) {
            $sql        = " SELECT *
                            FROM `" .self::$_DB_CONFIG['table']. "`
                            WHERE `" .self::$_DB_CONFIG['prefix']. "userid` = '" .$userid. "'
                            AND (
                                    (
                                        `" .self::$_DB_CONFIG['prefix']. "time` >= '" .(time() - $time). "' AND
                                        `" .self::$_DB_CONFIG['prefix']. "status` = 'ok'
                                    ) OR
                                    (
                                        `" .self::$_DB_CONFIG['prefix']. "status` IN ('wait', 'inprogress')
                                    )
                                )
                            ";
            $res        = Database::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            $data       = Array();
            foreach($res as $row) {
                $data[] = Utils::removePrefixToArrayKeys(self::$_DB_CONFIG['prefix'], $row);
            }

            return $data;
        }

        public static function getLastToUpdate($nbChars) {
            $sql = "SELECT *
                    FROM `" .self::$_DB_CONFIG['table']. "`
                    WHERE `" .self::$_DB_CONFIG['prefix']. "status` = 'wait'
                    ORDER BY `" .self::$_DB_CONFIG['prefix']. "time`
                    LIMIT 0, " .$nbChars;

            $res        = Database::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            $data       = Array();
            foreach($res as $row) {
                $current    = Utils::removePrefixToArrayKeys(self::$_DB_CONFIG['prefix'], $row);
                $data[]     = $current;

                // set status to `inprogress`

                /*
                $obj = new self($current);
                $obj->setDetail('status', 'inprogress');
                $obj->update();
                */
            }

            return $data;
        }

        /**
         * Gearman stuff
         */
        public static function initGearman($host = '127.0.0.1', $port = 4730) {
            $client = new GearmanClient();
            $client->addServer('127.0.0.1', 4730);

            return $client;
        }

        public static function doBackground($client, $data) {
            $client->doBackground("update_character", json_encode($data));
        }
    }