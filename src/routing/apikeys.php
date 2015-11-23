<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 12/05/15
 * Time: 01:23
 */

    ////////////////////
    // listing
    $app->get('/api-keys', function() use ($app) {
        $apikeys        = ApiKey::getAllForUser($_SESSION['user_id']);

        $keys           = Array(
            'ok'        => Array(),
            'pending'   => Array()
        );

        foreach($apikeys as $apikey) {
            $toInject = $apikey;

            if($apikey['status'] == 'ok') {
                $chars              = Character::getForApiKey($apikey['id']);
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

    ////////////////////
    // create
    $app->get('/api-keys/add', function() use ($app) {
        return $app['twig']->render('add-api-keys.twig');
    });
    $app->post('/api-keys/add', function() use ($app) {
        // register api key and list all characters
        $keyId      = $_POST['keyid'];
        $vCode      = $_POST['vcode'];

        $apikey = ApiKey::createdPendingOne(Array(
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
        $details    = ApiKey::get(Array('id' => $id));
        $apikey     = new ApiKey($details);

        $pheal      = new Pheal\Pheal($apikey->getDetail('keyId'), $apikey->getDetail('vCode'));
        $response   = $pheal->Characters();
        $characters = Array();

        foreach($response->characters as $character) {
            $charid         = $character->characterID;

            $db_create      = Array(
                'apikey'        => $id,
                'charid'        => $charid
            );

            $check = Character::exists($db_create);
            if(!$check) {
                // let's create character
                $data           = ApiKey::collectData($character, $pheal);

                //////////
                Character::create(array_merge($db_create, Array(
                    'isActive'      => 'N',
                    'informations'  => serialize($data['generic']),
                    'skills'        => serialize($data['skills']),
                    'created'       => time(),
                    'lastUpdate'    => time()
                )));

                $characterId        = Database::getInstance()->lastInsertId();
                $characterActive    = false;
            } else {
                // already exists in database, let's just load it and update datas
                $details            = Character::get(Array('id' => $check['id']));
                $characterObj       = new Character($details);
                $data               = ApiKey::collectData($character, $pheal);

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

        $apiKeyCharacters               = Character::getForApiKey($id);

        $checkedApiKeysCharacters       = Array();
        if(isset($_POST['characters']) && !empty($_POST['characters'])) {
            $checkedApiKeysCharacters   = $_POST['characters'];
        }

        // processing characters
        foreach($apiKeyCharacters as $currentCharacter) {
            $characterObj       = new Character($currentCharacter);

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
        $details        = ApiKey::get(Array('id' => $id));
        $apiKeyObj      = new ApiKey($details);

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
        $details    = ApiKey::get(Array('id' => $id));
        $apiKeyObj  = new ApiKey($details);
        $apiKeyObj->delete();

        return $app->redirect('/api-keys');
    });
