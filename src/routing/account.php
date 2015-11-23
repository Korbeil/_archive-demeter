<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 11/05/15
 * Time: 00:47
 */

    $app->get('/account', function() use ($app) {
        if(isset($_SESSION['isEveSSO']) && $_SESSION['isEveSSO']) {
            return $app->redirect('/');
        }
        return $app['twig']->render('account.twig');
    });
    $app->post('/account', function() use ($app) {
        $response = User::changePassword($_SESSION['user_id'], $_POST);

        if(!$response['done']) {
            $notification = new Notification($response['error']);
        } else {
            $notification = new Notification($response['success'], NOTIFICATION_SUCCESS);
        }
        GlobalVars::getInstance()->get('notifications')->add($notification);
        return $app->redirect('/account');
    });