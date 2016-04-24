<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 24/04/16
 * Time: 16:16
 */

    namespace Demeter\Controller;

    class LoginRegisterController extends \Demeter\Core\Controller {

        public static function route(\Silex\Application $app) {
            $app->get('/login', function() use ($app) {
                $eveLoginUrl = \Demeter\Utils\Utils::buildEveLoginURL();

                return $app['twig']->render('login.twig', Array(
                    'evesso'   => $eveLoginUrl
                ));
            });
            $app->get('/register', function() use ($app) {
                return $app['twig']->render('register.twig');
            });

            $app->post('/login', function() use ($app) {
                return \Demeter\Model\User::loginAndRegisterRoutine($app, 'login', $_POST);
            });
            $app->post('/register', function() use ($app) {
                return \Demeter\Model\User::loginAndRegisterRoutine($app, 'register', $_POST);
            });
            // callback for eve-sso login/register
            $app->get('/eve-sso', function() use ($app) {
                $user_id    = \Demeter\Model\User::processEveSSO();

                $_SESSION['user_id']    = $user_id;
                $_SESSION['isLogged']   = true;
                $_SESSION['isEveSSO']   = true;

                return $app->redirect('/');
            });

            // logout
            $app->get('/logout', function() use ($app) {

                $_SESSION['user_id']    = 0;
                unset($_SESSION['user_id']);

                $_SESSION['isLogged']   = false;
                $_SESSION['isEveSSO']   = false;

                return $app->redirect('/login');
            });
        }

    }