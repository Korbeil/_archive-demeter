<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 24/04/16
 * Time: 18:15
 */

    namespace Demeter\Controller;

    class CharacterController extends \Demeter\Core\Controller {

        public static function route(\Silex\Application $app) {
            $app->get('/character/update', function() use ($app) {
                $chars              = \Demeter\Model\Character::getActivesForUser($_SESSION['user_id']);
                $client             = \Demeter\Model\RequestQueue::initGearman();

                foreach($chars as $char) {
                    $requestData    = \Demeter\Model\RequestQueue::add($_SESSION['user_id'], $char['id']);
                    \Demeter\Model\RequestQueue::doBackground($client, $requestData);
                }

                $userDetails        = \Demeter\Model\User::get(Array('id' => $_SESSION['user_id']));
                $userObj            = new \Demeter\Model\User($userDetails);
                $userObj->setDetail('lastRequestedUpdate', time());
                $userObj->update();

                return $app->redirect('/');
            });
        }

    }