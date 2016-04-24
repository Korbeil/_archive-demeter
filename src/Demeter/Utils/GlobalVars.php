<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 24/04/16
 * Time: 17:20
 */

    namespace Demeter\Utils;

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