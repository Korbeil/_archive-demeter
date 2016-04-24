<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 09/05/15
 * Time: 01:38
 */

    namespace Demeter\Model;

    class User extends \Demeter\Core\DatabaseObject {

        static protected $_DB_CONFIG = array(
            'table'     => "user",
            'prefix'    => "usr_"
        );

        static protected $_DB_FIELDS = array(
            'type',
            'identifier',
            'password',
            'created',
            'lastConnection',
            'lastRequestedUpdate',
            'isAdmin'
        );

        //////////////////////////////////////////////////////////
        // EvE-SSO
        public static function processEveSSO() {
            $response   = self::processAuthCode();
            $character  = self::getCharacterId($response);

            $exists     = self::exists(Array(
                'type'          => 'eveo',
                'identifier'    => $character['CharacterID']
            ));

            if(!$exists) {
                self::create(Array(
                    'type'              => 'eveo',
                    'identifier'        => $character['CharacterID'],
                    'password'          => '',
                    'created'           => time(),
                    'lastConnection'    => time()
                ));
                $user_id = \Demeter\Core\Database::getInstance()->lastInsertId();

                return $user_id;
            } else {
                $user       = self::get(Array('id' => $exists['id']));
                $userObj    = new self($user);

                $userObj->setDetail('lastConnection', time());
                $userObj->update();

                return $exists['id'];
            }
        }
        public static function processCREST($url, $headers, $post_params = Array()) {
            // curl query
            $ch         = curl_init();

            $opts       = Array(
                CURLOPT_URL             => $url,
                CURLOPT_HTTPHEADER      => $headers,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_USERAGENT       => 'iveeCrest/1.0',
                CURLOPT_SSL_VERIFYPEER  => true,
                CURLOPT_SSL_CIPHER_LIST => 'TLSv1' //prevent protocol negotiation fail
            );
            if(count($post_params) > 0) {
                $opts[CURLOPT_POST]         = true;
                $opts[CURLOPT_POSTFIELDS]   = http_build_query($post_params);
            }

            curl_setopt_array($ch, $opts);

            // curl response
            $resBody    = curl_exec($ch);
            $info       = curl_getinfo($ch);
            $err        = curl_errno($ch);
            $errMsg     = curl_error($ch);

            if ($err != 0) {
                throw new \Demeter\Exception\EveSSOException($errMsg, $err);
            }
            if (!in_array($info['http_code'], array(200, 302))) {
                $errMsg = 'HTTP response not OK: ' . (int)$info['http_code'] . '. Response body: ' . $resBody;
                throw new \Demeter\Exception\EveSSOException($errMsg, $info['http_code']);
            }

            curl_close($ch);
            $response = json_decode($resBody, true);
            return $response;
        }
        public static function processAuthCode() {
            $params             = \Demeter\Utils\GlobalVars::getInstance()->get('eve-sso');
            $header             = 'Authorization: Basic ' . base64_encode($params['client_id'] . ':' . $params['secret_key']);
            $url                = $params['base_url'] . $params['token_url'];

            // fields
            $fields = array(
                'grant_type'    => 'authorization_code',
                'code'          => $_GET['code']
            );

            return self::processCREST($url, Array($header), $fields);
        }
        public static function getCharacterId($response) {
            $params             = \Demeter\Utils\GlobalVars::getInstance()->get('eve-sso');
            $header             = 'Authorization: ' .$response['token_type']. ' ' .$response['access_token'];
            $url                = $params['base_url'] . $params['verify_url'];

            return self::processCREST($url, Array($header));
        }

        //////////////////////////////////////////////////////////
        // register
        public static function register($array) {
            // check for passwords
            if($array['password'] != $array['password-copy']) {
                return Array(
                    'done'  => false,
                    'error' => 'Passwords don\'t match'
                );
            }
            if(strlen($array['password']) < 6) {
                return Array(
                    'done'  => false,
                    'error' => 'Passwords must be at least 6 characters.'
                );
            }

            self::create(Array(
                'type'              => 'email',
                'identifier'        => $array['email'],
                'password'          => self::hashPassword($array['password']),
                'created'           => time(),
                'lastConnection'    => time()
            ));
            $user_id = \Demeter\Core\Database::getInstance()->lastInsertId();

            return Array(
                'done'  => true,
                'user'  => $user_id
            );
        }

        //////////////////////////////////////////////////////////
        // login
        public static function login($array) {
            $exists = self::exists(Array(
                'type'          => 'email',
                'identifier'    => $array['email'],
                'password'      => self::hashPassword($array['password'])
            ));

            if(!$exists) {
                return Array(
                    'done'  => false,
                    'error' => 'Wrong Email and Password combination'
                );
            } else {
                return Array(
                    'done'  => true,
                    'user'  => $exists['id']
                );
            }
        }

        //////////////////////////////////////////////////////////
        // login & register utils
        public static function loginAndRegisterRoutine($app, $type, $array) {
            $response = \Demeter\Model\User::$type($array);

            if(!$response['done']) {
                $notification = new \Demeter\Notification\Notification($response['error']);
                GlobalVars::getInstance()->get('notifications')->add($notification);

                return $app->redirect('/' .$type);
            } else {
                $_SESSION['user_id']    = $response['user'];
                $_SESSION['isLogged']   = true;

                return $app->redirect('/');
            }
        }

        //////////////////////////////////////////////////////////
        // change password
        public static function changePassword($user_id, $data) {
            $details    = self::get(Array('id' => $user_id));
            $user       = new self($details);

            if(!self::checkPassword($data['old-password'], $user->getDetail('password'))) {
                return Array(
                    'done'  => false,
                    'error' => 'Old password is wrong.'
                );
            }

            if($data['new-password'] != $data['new-password-copy']) {
                return Array(
                    'done'  => false,
                    'error' => 'New passwords doesn\'t match.'
                );
            }
            if(strlen($data['new-password']) < 6) {
                return Array(
                    'done'  => false,
                    'error' => 'Passwords must be at least 6 characters.'
                );
            }

            $user->setDetail('password', self::hashPassword($data['new-password']));
            $user->update();
            return Array(
                'done'      => true,
                'success'   => 'Password changed.'
            );
        }

        //////////////////////////////////////////////////////////
        // password utils
        protected static function hashPassword($password) {
            $_HASH_POWER    = 10;
            $hashedPassword = $password;

            for($i = 0; $i <= $_HASH_POWER; $i++) {
                $hashedPassword = sha1($hashedPassword);
            }
            return $hashedPassword;
        }
        protected static function checkPassword($password, $hashedPassword) {
            if(self::hashPassword($password) == $hashedPassword) {
                return true;
            }
            return false;
        }

        //////////////////////////////////////////////////////////
        // character update check
        public static function isUpdateAvailable($user_id) {
            $userDetails    = self::get(Array('id' => $user_id));
            $userObj        = new self($userDetails);
            $requestQueue   = \Demeter\Model\RequestQueue::checkForUserInTime($user_id);

            if(empty($requestQueue)) {
                // nothing in queue, okay, let's go ;)
                if($userObj->getDetail('lastRequestedUpdate') > 0) {
                    $last = ': ' .date('d/m/Y \a\t H:i:s', $userObj->getDetail('lastRequestedUpdate')). ', ';
                } else {
                    $last = ' never ... ';
                }

                return Array(
                    'message'   => 'ok',
                    'class'     => 'info',
                    'last'      => $last
                );
            } else {
                // something in queue, ERROR
                return Array(
                    'message'   => 'error',
                    'class'     => 'warning'
                );
            }
        }
    }