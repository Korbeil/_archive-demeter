<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 06/05/15
 * Time: 08:24
 */

    require_once __DIR__. '/require.php';

    //////////////////////////////////////////////////////////
    // load config
    require_once __DIR__. '/../config.php';

    //////////////////////////////////////////////////////////
    // init things
    require_once __DIR__. '/init.php';

    //////////////////////////////////////////////////////////
    // dashboard
    $app->get('/', function() use($app) {
        //echo '<pre>';
        //print_r(Character::getActivesForUser($_SESSION['user_id']));
        //echo '</pre>';

        return $app['twig']->render('dashboard.twig', Array(
            'update'        => User::isUpdateAvailable($_SESSION['user_id']),
            'characters'    => Character::getActivesForUser($_SESSION['user_id']),
            'apikeys'       => ApiKey::getAllLabelsForUser($_SESSION['user_id'])
        ));
    });

    $app->get('/character/update', function() use ($app) {
        $chars = Character::getActivesForUser($_SESSION['user_id']);

        $client = new GearmanClient();
        $client->addServer('127.0.0.1', 4730);

        foreach($chars as $char) {
            $requestData = RequestQueue::add($_SESSION['user_id'], $char['id']);
            $client->doBackground("update_character", json_encode($requestData ));
        }

        $userDetails    = User::get(Array('id' => $_SESSION['user_id']));
        $userObj        = new User($userDetails);
        $userObj->setDetail('lastRequestedUpdate', time());
        $userObj->update();

        return $app->redirect('/');
    });

    //////////////////////////////////////////////////////////
    // login/register part
    require_once __DIR__. '/routing/loginAndRegister.php';

    //////////////////////////////////////////////////////////
    // account part
    require_once __DIR__. '/routing/account.php';

    //////////////////////////////////////////////////////////
    // api-keys part
    require_once __DIR__. '/routing/apikeys.php';

    //////////////////////////////////////////////////////////
    // error
    require_once __DIR__. '/routing/error.php';
