<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 11/05/15
 * Time: 00:43
 */

    // debug
    $app['debug'] = true;

    // twig
    $app->register(new Silex\Provider\TwigServiceProvider(), Array(
        'twig.path'     => __DIR__.'/views/',
        'twig.options'  => Array(
            'debug'     => true
        )
    ));
    $app['twig']->addGlobal('session', $_SESSION);

    // notifications
    $notifObj = NULL;
    if(isset($_SESSION['notification_manager']) && !empty($_SESSION['notification_manager'])) {
        $notifObj = unserialize($_SESSION['notification_manager']);
    } else {
        $notifObj = new NotificationManager();
    }
    GlobalVars::getInstance()->set('notifications', $notifObj);
    $app['twig']->addGlobal('notifications', $notifObj->render());

    //////////////////////////////////////////////////////////
    // check for connection
    $app->before(function (Symfony\Component\HttpFoundation\Request $request, Silex\Application $app) {
        if( isset($_SESSION['isLogged']) && $_SESSION['isLogged']) {
            return;
        }

        $requestUri = $request->getRequestUri();
        $requestUri = explode('?', $requestUri);
        $requestUri = $requestUri[0];

        if( $requestUri != '/login' &&
            $requestUri != '/register' &&
            $requestUri != '/eve-sso') {

            return $app->redirect('/login');
        }
    }, Silex\Application::EARLY_EVENT);
