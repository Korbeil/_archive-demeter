<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 23/11/15
 * Time: 23:05
 */

    // init
    $loader = require_once __DIR__. '/../vendor/autoload.php';
    $loader->add('Demeter', __DIR__.'/../src/');

    // load config
    $app    = new \Demeter\Core\Application();
    $app->config();

    $worker = new GearmanWorker();
    $worker->addServer('127.0.0.1', 4730);

    // init singleton for Pheal
    \Pheal\Core\Config::getInstance()->cache = new \Pheal\Cache\RedisStorage();
    \Pheal\Core\Config::getInstance()->access = new \Pheal\Access\StaticCheck();

    /**
     * $workload contains "RequestQueue" details
     */
    $worker->addFunction("update_character", function(GearmanJob $job) {
        $workload       = json_decode($job->workload(), true);
        $requestObj     = new \Demeter\Model\RequestQueue($workload);

        $charDetails    = \Demeter\Model\Character::get(Array('id' => $workload['charid']));
        $charObj        = new \Demeter\Model\Character($charDetails);

        $apiKeyDetails  = \Demeter\Model\ApiKey::get(Array('id' => $charObj->getDetail('apikey')));
        $apiKeyObj      = new \Demeter\Model\ApiKey($apiKeyDetails);

        $pheal          = new Pheal\Pheal($apiKeyObj->getDetail('keyId'), $apiKeyObj->getDetail('vCode'), 'char');
        $phealColonies  = $pheal->PlanetaryColonies(Array("characterID" => $charDetails['charid']));

        $coloniesData   = Array();

        foreach($phealColonies->colonies as $planetary) {
            $planetID   = (string) $planetary['planetID'];
            $planetName = (string) $planetary['planetName'];

            $coloniesData[$planetID]    = Array(
                'id'                    => $planetID,
                'name'                  => $planetName,
                'extractors'            => Array(),
                'launchpad'             => Array(),
                'launchpadRemaining'    => 10000,
                'storage'               => Array()
            );

            $phealPins = $pheal->PlanetaryPins(Array(
                "characterID"   => $charDetails['charid'],
                "planetID"      => $planetID
            ));

            foreach($phealPins->pins as $pin) {
                $pinId      = (string) $pin->pinID;
                $pinType    = (string) $pin->typeName;

                if(strpos($pinType,' Extractor Control Unit') !== false) {
                    // is an extractor
                    $dateInstall    = DateTime::createFromFormat('Y-m-d H:i:s', $pin['installTime']);
                    $dateExpiry     = DateTime::createFromFormat('Y-m-d H:i:s', $pin['expiryTime']);

                    $currentPin = Array(
                        'id'        => (string) $pin['pinID'],
                        'type'      => 'extractor',
                        'install'   => $dateInstall->getTimestamp(),
                        'expiry'    => $dateExpiry->getTimestamp()
                    );

                    $coloniesData[$planetID]['extractors'][$currentPin['id']] = $currentPin;
                }
                if( strpos($pinType,' Launchpad') !== false ||
                    strpos($pinType,' Storage Facility') !== false) {
                    // is a launchpad or storage
                    $type = '';
                    if(strpos($pinType,' Launchpad') !== false) {
                        $type = 'launchpad';
                    }
                    if(strpos($pinType,' Storage Facility') !== false) {
                        $type = 'storage';
                    }

                    $contentTypeID  = (string) $pin->contentTypeID;
                    $item           = \Demeter\Model\EveItem::get(Array('typeID' => $contentTypeID));

                    $itemTotalMass  = $pin->contentQuantity * $item['volume'];

                    $currentPin = Array(
                        'id'        => $item['typeID'],
                        'name'      => $item['typeName'],
                        'quantity'  => (string) $pin->contentQuantity,
                        'mass'      => $itemTotalMass,
                        'icon'      => 'https://image.eveonline.com/Type/' .$item['typeID']. '_32.png'
                    );

                    switch($type) {
                        case 'launchpad':
                            $coloniesData[$planetID][$type][$item['typeID']]   = $currentPin;
                            $coloniesData[$planetID]['launchpadRemaining']    -= $itemTotalMass;
                            break;
                        case 'storage':
                            if(!isset($coloniesData[$planetID][$type][$pinId])) {
                                $coloniesData[$planetID][$type][$pinId] = Array(
                                    'content'           => Array(),
                                    'storageRemaining'  => 12000
                                );
                            }
                            $coloniesData[$planetID][$type][$pinId]['content'][$item['typeID']]  = $currentPin;
                            $coloniesData[$planetID][$type][$pinId]['storageRemaining']         -= $itemTotalMass;
                            break;
                    }
                }
            }
        }

        // let's now update data
        $charObj->setDetail('hasRequested', 'N');
        $charObj->setDetail('planets', serialize($coloniesData));
        $charObj->update();
        echo 'Character: `' .$charObj->getId(). '` updated !'."\n";

        // and update the queue with a new 'done' status :)
        $requestObj->setDetail('status', 'done');
        $requestObj->update();

        // remove database connection
        \Demeter\Core\Database::getInstance()->destroy();
    });

    while ($worker->work());