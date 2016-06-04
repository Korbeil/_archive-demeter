<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 24/04/16
 * Time: 16:19
 */

    namespace Demeter\Controller;

    class ApiKeysController extends \Demeter\Core\Controller {

        public static function route(\Silex\Application $app) {
            ////////////////////
            // listing
            $app->get('/api-keys', function() use ($app) {
                $apikeys        = \Demeter\Model\ApiKey::getAllForUser($_SESSION['user_id']);

                $keys           = Array(
                    'ok'        => Array(),
                    'pending'   => Array()
                );

                foreach($apikeys as $apikey) {
                    $toInject = $apikey;

                    if($apikey['status'] == 'ok') {
                        $chars              = \Demeter\Model\Character::getForApiKey($apikey['id']);
                        $apikey['chars']    = Array();
                        foreach($chars as $char) {
                            if($char['isActive'] == 'Y') {
                                $apikey['chars'][] = $char;
                            }
                        }
                    }

                    $keys[$apikey['status']][] = $apikey;
                }

                return $app['twig']->render('api-keys.twig', Array(
                    'apikeys'   => $keys
                ));
            });

            $app->post('/api-keys', function() use ($app) {
                $id         = $_POST['inputApiKey'];
                $label      = $_POST['inputLabel'];

                $details    = \Demeter\Model\ApiKey::get(Array('id' => $id));
                $apiKeyObj  = new \Demeter\Model\ApiKey($details);

                $apiKeyObj->setDetail('name', $label);
                $apiKeyObj->update();

                return $app->redirect('/api-keys');
            });

            ////////////////////
            // create
            $app->get('/api-keys/add', function() use ($app) {
                return $app['twig']->render('add-api-keys.twig');
            });
            $app->post('/api-keys/add', function() use ($app) {
                // register api key and list all characters
                $keyId      = $_POST['keyid'];
                $vCode      = $_POST['vcode'];

                $apikey = \Demeter\Model\ApiKey::createdPendingOne(Array(
                    'user'  => $_SESSION['user_id'],
                    'name'  => $_POST['name'],
                    'keyId' => $keyId,
                    'vCode' => $vCode
                ));
                return $app->redirect('/api-keys/edit/' .$apikey);
            });

            ////////////////////
            // update
            $app->get('/api-keys/edit/{id}', function($id) use ($app) {
                $apikeys    = \Demeter\Model\ApiKey::getAllForUser($_SESSION['user_id']);
                $exists     = false;
                foreach($apikeys as $apikey) {
                    if($apikey['id'] == $id) {
                        $exists = true;
                    }
                }
                if(!$exists) {
                    return $app->redirect('/api-keys');
                }

                $details    = \Demeter\Model\ApiKey::get(Array('id' => $id));
                $apikey     = new \Demeter\Model\ApiKey($details);


                $client     = \Demeter\Model\RequestQueue::initGearman();
                $pheal      = new \Pheal\Pheal($apikey->getDetail('keyId'), $apikey->getDetail('vCode'));
                $response   = $pheal->Characters();
                $characters = Array();

                foreach($response->characters as $character) {
                    $charid         = $character->characterID;

                    $db_create      = Array(
                        'apikey'        => $id,
                        'charid'        => $charid
                    );

                    $check = \Demeter\Model\Character::exists($db_create);
                    if(!$check) {
                        // let's create character
                        $data           = \Demeter\Model\ApiKey::collectData($character, $pheal);

                        //////////
                        \Demeter\Model\Character::create(array_merge($db_create, Array(
                            'isActive'      => 'N',
                            'informations'  => serialize($data['generic']),
                            'skills'        => serialize($data['skills']),
                            'created'       => time(),
                            'lastUpdate'    => time()
                        )));

                        $characterId        = \Demeter\Core\Database::getInstance()->lastInsertId();
                        $characterActive    = false;

                        // force update character after character creation
                        $requestData        = \Demeter\Model\RequestQueue::add($_SESSION['user_id'], $characterId);
                        \Demeter\Model\RequestQueue::doBackground($client, $requestData);
                    } else {
                        // already exists in database, let's just load it and update datas
                        $details            = \Demeter\Model\Character::get(Array('id' => $check['id']));
                        $characterObj       = new \Demeter\Model\Character($details);
                        $data               = \Demeter\Model\ApiKey::collectData($character, $pheal);

                        $characterObj->setDetail('informations', serialize($data['generic']));
                        $characterObj->setDetail('skills'      , serialize($data['skills']));
                        $characterObj->setDetail('lastUpdate'  , time());
                        $characterObj->update();

                        $characterId        = $characterObj->getId();
                        $characterActive    = ($characterObj->getDetail('isActive') == 'Y');
                    }

                    $data = array_merge(Array(
                        'id'        => $characterId,
                        'active'    => $characterActive
                    ), $data);

                    $characters[]   = $data;
                }

                return $app['twig']->render('characters-api-key.twig', Array(
                    'characters'    => $characters
                ));
            });
            $app->post('/api-keys/edit/{id}', function($id) use ($app) {

                $apiKeyCharacters               = \Demeter\Model\Character::getForApiKey($id);

                $checkedApiKeysCharacters       = Array();
                if(isset($_POST['characters']) && !empty($_POST['characters'])) {
                    $checkedApiKeysCharacters   = $_POST['characters'];
                }

                // processing characters
                foreach($apiKeyCharacters as $currentCharacter) {
                    $characterObj       = new \Demeter\Model\Character($currentCharacter);

                    if(in_array($currentCharacter['id'], $checkedApiKeysCharacters)) {
                        // active
                        $characterObj->setDetail('isActive', 'Y');
                        $characterObj->setDetail('lastUpdate'  , time());
                    } else {
                        // not active
                        $characterObj->setDetail('isActive', 'N');
                        $characterObj->setDetail('lastUpdate'  , time());
                    }
                    $characterObj->update();
                }

                // processing apikey
                $details        = \Demeter\Model\ApiKey::get(Array('id' => $id));
                $apiKeyObj      = new \Demeter\Model\ApiKey($details);

                $apiKeyObj->setDetail('status', 'ok');
                $apiKeyObj->update();

                return $app->redirect('/api-keys');
            });

            ////////////////////
            // remove
            $app->get('/api-keys/remove/{id}', function($id) use ($app) {
                return $app['twig']->render('delete-api-keys.twig');
            });
            $app->post('/api-keys/remove/{id}', function($id) use ($app) {
                $details    = \Demeter\Model\ApiKey::get(Array('id' => $id));
                $apiKeyObj  = new \Demeter\Model\ApiKey($details);
                $apiKeyObj->delete();

                return $app->redirect('/api-keys');
            });
        }

    }