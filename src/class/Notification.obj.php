<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 10/05/15
 * Time: 01:57
 */

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

    class Notification
    {
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

    class NotificationManager
    {
        public      $notifications;

        public function __construct() {
            $this->reset();
        }

        public function add( $notification) {
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
