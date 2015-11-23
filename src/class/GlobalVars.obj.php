<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 09/05/15
 * Time: 02:02
 */

    class GlobalVars {

        public $_details;

        // singleton
        private static $_instance;

        public function __construct() {
            $this->_details = array();
        }

        private function __clone () {
            trigger_error("Clone is denied on a Singleton.", E_USER_ERROR);
        }

        public static function getInstance () {
            if (!(self::$_instance instanceof self))
                self::$_instance = new self();

            return self::$_instance;
        }

        public function exists($name) {
            return isset($this->_details[$name]);
        }
        public function get($name) {
            return ((isset($this->_details[$name])) ? $this->_details[$name] : '');
        }
        public function set($name, $value) {
            $this->_details[$name] = $value;
        }
    }