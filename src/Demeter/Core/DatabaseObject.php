<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 09/05/15
 * Time: 01:38
 */

    namespace Demeter\Core;

    class DatabaseObject {

        protected $id;
        protected $details;

        protected $hasId = true;

        static protected $_DB_CONFIG = array();
        static protected $_DB_FIELDS = array();

        //////////////////////////////////////////////////////////
        // construct
        public function __construct($details = array()) {


            if(isset($details['id']) && $this->hasId) {
                $this->id = $details['id'];
            } else {
                $this->id = 0;
            }

            $this->details = $details;
        }

        //////////////////////////////////////////////////////////
        // db_fields
        public function getFields($withPrefix = false, $withId = true) {
            $fields = static::$_DB_FIELDS;
            if($withId && $this->hasId) {
                $fields = array_merge( array('id'), $fields);
            }

            if($withPrefix) {
                $dbConf = self::getDbConf();
                return \Demeter\Utils\Utils::addPrefixToArray($dbConf['prefix'], $fields);
            }
            return $fields;
        }

        //////////////////////////////////////////////////////////
        // details
        public function getId() {
            return $this->id;
        }
        public function getDetail($name) {
            return (isset($this->details[$name])) ? $this->details[$name] : '';
        }
        public function setDetail($name,$value) {
            if(in_array($name, $this->getFields())) {
                $this->details[$name] = $value;
                return true;
            }
            return false;
        }
        public function getDetails() {
            return $this->details;
        }

        //////////////////////////////////////////////////////////
        // db_conf
        public static function getDbConf() {
            return static::$_DB_CONFIG;
        }

        //////////////////////////////////////////////////////////
        // sql utils
        protected static function makeWhere($details, $dbConf) {
            $sql = '';
            $first = true;
            foreach($details as $key => $detail) {
                if(is_array($detail)) {
                    switch($detail['type']) {
                        case 'gt':
                            $type = '>';
                            break;
                        case 'lt':
                            $type = '<';
                            break;
                        case 'gte':
                            $type = '>=';
                            break;
                        case 'lte':
                            $type = '<=';
                            break;
                    }

                    if($first) {
                        $sql .= ' `' .$dbConf['prefix'].$key. '` ' .$type. ' \'' .$detail['value']. '\'';
                        $first = false;
                    } else {
                        $sql .= ' AND `' .$dbConf['prefix'].$key. '` ' .$type. ' \'' .$detail['value']. '\'' ;
                    }
                } else {
                    if($first) {
                        $sql .= ' ' .$dbConf['prefix'].$key. ' = \'' .$detail. '\'';
                        $first = false;
                    } else {
                        $sql .= ' AND `' .$dbConf['prefix'].$key. '` = \'' .$detail. '\'' ;
                    }
                }
            }
            return $sql;
        }
        protected function makeUpdate() {
            $dbConf = static::getDbConf();

            $parts = array();
            foreach($this->getDetails() as $key => $detail) {
                $parts[] = '`' .$dbConf['prefix'].$key. '` = \'' .addslashes($detail). '\'';
            }
            return implode(', ', $parts);
        }

        //////////////////////////////////////////////////////////
        // create
        public static function create($details) {
            $dbConf = static::getDbConf();

            $keys   = array_keys($details);
            $sql    = 'INSERT INTO `' .$dbConf['table']. '` (' .implode(', ', \Demeter\Utils\Utils::addPrefixToArray($dbConf['prefix'], $keys)). ') VALUES (' .implode(', ', \Demeter\Utils\Utils::addPrefixAndSufixToArray('\'', $details)). ');';
            if(\Demeter\Core\Database::getInstance()->exec($sql)) {
                return \Demeter\Core\Database::getInstance()->lastInsertId();
            }

            return false;
        }
        // delete
        public function delete() {
            // can't delete tables with no id
            if(!$this->hasId) {
                return false;
            }

            $dbConf = static::getDbConf();

            $sql = 'DELETE FROM `' .$dbConf['table']. '` WHERE ' .self::makeWhere(array('id' => $this->getId()), $dbConf);
            return \Demeter\Core\Database::getInstance()->exec($sql);
        }
        // update
        public function update() {
            // can't update tables with no id
            if(!$this->hasId) {
                return false;
            }

            $dbConf = static::getDbConf();

            $sql = 'UPDATE `' .$dbConf['table']. '` SET ' .$this->makeUpdate(). ' WHERE ' .$dbConf['prefix']. 'id = \'' .$this->getId(). '\'';
            return \Demeter\Core\Database::getInstance()->exec($sql);
        }
        // exists
        public static function exists($details) {
            $dbConf = static::getDbConf();

            $sql = 'SELECT ' .$dbConf['prefix']. 'id as id FROM `' .$dbConf['table']. '` WHERE ' .self::makeWhere($details, $dbConf);
            $stm = \Demeter\Core\Database::getInstance()->query($sql);

            if($stm) {
                return $stm->fetch();
            }
            return false;
        }
        // get
        public static function get($details) {
            $dbConf = static::getDbConf();

            $sql    = 'SELECT * FROM `' .$dbConf['table']. '` WHERE ' .self::makeWhere($details, $dbConf);
            $res    = \Demeter\Core\Database::getInstance()->query($sql);

            if($res instanceof \PDOStatement) {
                $data   = $res->fetch();

                if($dbConf['prefix'] != '') {
                    return \Demeter\Utils\Utils::removePrefixToArrayKeys($dbConf['prefix'], $data);
                } else {
                    return $data;
                }
            } else {
                // error
                echo '<pre>';
                var_dump('--------------------------------');
                var_dump('ERROR: ' .print_r(\Demeter\Core\Database::getInstance()->errorInfo(), true));
                var_dump($sql);
                var_dump('stacktrace: ');
                debug_print_backtrace();
                var_dump('--------------------------------');

                return Array();
            }
        }
        // get
        public static function getAll($details) {
            $dbConf     = static::getDbConf();

            $sql        = 'SELECT * FROM `' .$dbConf['table']. '` WHERE ' .self::makeWhere($details, $dbConf);
            $data       = \Demeter\Core\Database::getInstance()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

            $toReturn   = Array();
            foreach($data as $row) {
                $toReturn[] = \Demeter\Utils\Utils::removePrefixToArrayKeys($dbConf['prefix'], $row);
            }
            return $toReturn;
        }

    }