<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 09/05/15
 * Time: 01:41
 */


    class Database extends PDO {

        // singleton
        private static $_instance;

        public function __construct() {
            $database = GlobalVars::getInstance()->get('database');
            parent::__construct($database['dsn'], $database['user'], $database['pass']);
        }

        private function __clone () {
            trigger_error("Clone is denied on a Singleton.", E_USER_ERROR);
        }

        public static function getInstance () {
            if (!(self::$_instance instanceof self))
                self::reset();

            return self::$_instance;
        }

        public static function reset() {
            self::$_instance = new self();
        }
    }