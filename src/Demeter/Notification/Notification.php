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

    class Notification {
        public $type;
        public $message;

        public function __construct( $message, $type = NOTIFICATION_ERROR) {
            $this->type     = $type;
            $this->message  = $message;
        }

        public function render() {
            global $NOTIFICATION_MESSAGES;

            $html = '';
            $html .= '<div class="container alert alert-'.$this->type.'" style="width: 100%;">';
            $html .= $NOTIFICATION_MESSAGES[$this->type].$this->message;
            $html .= '<a href="javascript:;" onclick="hideNotification(this);" style="float: right; color: inherit; text-decoration: none;"><span aria-hidden="true">&times;</span></a>';
            $html .= '</div>';

            return $html;
        }
    };