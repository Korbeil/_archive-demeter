<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 24/04/16
 * Time: 17:18
 */

    namespace Demeter\Utils;

    class Utils {

        //////////////////////////////////////////////////////////
        // database utils
        public static function addPrefixToArray($prefix, $array) {
            $newArray = array();
            foreach($array as $value) {
                $newArray[] = $prefix.$value;
            }
            return $newArray;
        }

        public static function removePrefixToArrayKeys($prefix, $array) {
            $newArray = array();
            foreach($array as $key => $value) {
                if(is_string($key)) {
                    $newKey = explode($prefix, $key);
                    $newArray[$newKey[1]] = $value;
                }
            }
            return $newArray;
        }

        public static function addPrefixAndSufixToArray($toAppend, $array) {
            $newArray = array();
            foreach($array as $value) {
                $newArray[] = $toAppend.addslashes($value).$toAppend;
            }
            return $newArray;
        }

        public static function buildParamsForURL($params) {
            $parts          = Array();
            foreach($params as $key => $value) {
                $parts[]    = $key. '=' .$value;
            }
            return implode('&', $parts);
        }

        //////////////////////////////////////////////////////////
        // eve utils
        public static function buildEveLoginURL($scopes = Array()) {
            $params = \Demeter\Utils\GlobalVars::getInstance()->get('eve-sso');
            $base   = $params['base_url'] . $params['auth_url'];

            $array  = Array(
                'response_type' => 'code',
                'redirect_uri'  => $params['callback'],
                'client_id'     => $params['client_id'],
                'scope'         => implode('+', $scopes),
                'state'         => uniqid('demeter_')
            );

            return $base. '?' .self::buildParamsForURL($array);
        }

    }