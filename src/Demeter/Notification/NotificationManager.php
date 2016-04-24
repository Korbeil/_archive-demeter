<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 24/04/16
 * Time: 16:08
 */

    namespace Demeter\Notification;

    // init vars
    if(!defined('NOTIFICATION_INFO')) {
        define( 'NOTIFICATION_INFO'     , 'info');
        define( 'NOTIFICATION_SUCCESS'  , 'success');
        define( 'NOTIFICATION_WARNING'  , 'warning');
        define( 'NOTIFICATION_ERROR'    , 'danger');

        $NOTIFICATION_MESSAGES = array(
            'info'	    =>	'Information : ',
            'success'	=>	'Success : ',
            'warning'	=>	'Warning : ',
            'danger'	=>	'Error : ',
        );
    }

    class NotificationManager {
        public      $notifications;

        public function __construct() {
            $this->reset();
        }

        public function add(\Demeter\Notification\Notification $notification) {
            $this->notifications[] = $notification;
            $this->save();
        }

        public function save() {
            $_SESSION['notification_manager'] = serialize($this);
        }

        public function reset() {
            $this->notifications = array();
            $this->save();
        }

        public function isNotEmpty() {
            return count($this->notifications) > 0;
        }

        public function render() {
            $html = '';
            if( count($this->notifications) > 0) {
                foreach( $this->notifications as $notification) {
                    $html .= $notification->render();
                }
                $this->reset();
            }
            return $html;
        }
    };