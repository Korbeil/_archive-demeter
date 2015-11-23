<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 11/05/15
 * Time: 00:40
 */

    $app->get('/login', function() use ($app) {
        $eveLoginUrl = Utils::buildEveLoginURL();

        return $app['twig']->render('login.twig', Array(
            'evesso'   => $eveLoginUrl
        ));
    });
    $app->get('/register', function() use ($app) {
        return $app['twig']->render('register.twig');
    });

    $app->post('/login', function() use ($app) {
        return User::loginAndRegisterRoutine($app, 'login', $_POST);
    });
    $app->post('/register', function() use ($app) {
        return User::loginAndRegisterRoutine($app, 'register', $_POST);
    });
    // callback for eve-sso login/register
    $app->get('/eve-sso', function() use ($app) {
        $user_id    = User::processEveSSO();

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