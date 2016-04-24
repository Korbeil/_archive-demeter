<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 24/04/16
 * Time: 16:04
 */

    namespace Demeter\Controller;

    class DashboardController extends \Demeter\Core\Controller {

        public static function route(\Silex\Application $app) {
            $app->get('/', function() use($app) {
                return $app['twig']->render('dashboard.twig', Array(
                    'update'        => \Demeter\Model\User::isUpdateAvailable($_SESSION['user_id']),
                    'characters'    => \Demeter\Model\Character::getActivesForUser($_SESSION['user_id']),
                    'apikeys'       => \Demeter\Model\ApiKey::getAllLabelsForUser($_SESSION['user_id'])
                ));
            });
        }

    }