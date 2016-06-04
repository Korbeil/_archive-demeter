<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 28/04/16
 * Time: 14:15
 */


    namespace Demeter\Controller;

    class CrestCharacterController extends \Demeter\Core\Controller {

        public static function route(\Silex\Application $app) {
            ////////////////////
            // listing
            $app->get('/characters', function() use ($app) {

                // auth-url
                $params                 = \Demeter\Utils\GlobalVars::getInstance()->get('eve-sso');
                $provider               = new \Killmails\OAuth2\Client\Provider\EveOnline([
                    'clientId'          => $params['client_id'],
                    'clientSecret'      => $params['secret_key'],
                    'redirectUri'       => $params['callback']
                ]);
                $options                = [
                    'scope'             => ['characterAssetsRead']
                ];
                $eveLoginUrl            = $provider->getAuthorizationUrl($options);

                // characters
                $characters             = \Demeter\Model\CrestCharacter::getAll(Array(
                    'user'              => $_SESSION['user_id'],
                    'status'            => 'ok'
                ));

                return $app['twig']->render('CrestCharacter/characters.twig', Array(
                    'characters'        => $characters,
                    'createURL'         => $eveLoginUrl
                ));
            });

            ////////////////////
            // remove
            $app->get('/characters/remove/{id}', function($id) use ($app) {
                return $app['twig']->render('CrestCharacter/delete.twig');
            });
            $app->post('/characters/remove/{id}', function($id) use ($app) {
                $details        = \Demeter\Model\CrestCharacter::get(Array('id' => $id));
                $characterObj   = new \Demeter\Model\CrestCharacter($details);
                $characterObj->setDetail('status', 'deleted');
                $characterObj->update();

                return $app->redirect('/characters');
            });
        }

        public static function routeSSO() {
            $params                 = \Demeter\Utils\GlobalVars::getInstance()->get('eve-sso');
            $provider               = new \Killmails\OAuth2\Client\Provider\EveOnline([
                'clientId'          => $params['client_id'],
                'clientSecret'      => $params['secret_key'],
                'redirectUri'       => $params['callback']
            ]);
            $token                  = $provider->getAccessToken('authorization_code', [
                'code'              => $_GET['code']
            ]);
            $user                   = $provider->getResourceOwner($token);
            $user_id                = $_SESSION['user_id'];

            $crestCharacterId       = \Demeter\Model\CrestCharacter::create(Array(
                'user'              => $user_id,
                'characterId'       => $user->getCharacterId(),
                'characterName'     => $user->getCharacterName(),
                'accessToken'       => serialize($token)
            ));

            return true;
        }

    }