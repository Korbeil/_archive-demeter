<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 24/04/16
 * Time: 16:07
 */

    namespace Demeter\Controller;

    class AccountController extends \Demeter\Core\Controller {

        public static function route(\Silex\Application $app) {

            $app->get('/account', function() use ($app) {
                if(isset($_SESSION['isEveSSO']) && $_SESSION['isEveSSO']) {
                    return $app->redirect('/');
                }
                return $app['twig']->render('account.twig');
            });
            $app->post('/account', function() use ($app) {
                $response = \Demeter\Model\User::changePassword($_SESSION['user_id'], $_POST);

                if(!$response['done']) {
                    $notification = new \Demeter\Notification\Notification($response['error']);
                } else {
                    $notification = new \Demeter\Notification\Notification($response['success'], NOTIFICATION_SUCCESS);
                }
                \Demeter\Utils\GlobalVars::getInstance()->get('notifications')->add($notification);
                return $app->redirect('/account');
            });

        }

    }