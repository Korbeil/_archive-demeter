<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 07/06/15
 * Time: 19:21
 */

    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../src/require.php';
    require_once __DIR__ . '/../config.php';

    while(true) {
        $nbQueries          = 0;
        $requestPerSeconds  = (1+6)*2;  // 14/30
        $requestQueueData   = RequestQueue::getLastToUpdate(5);

        $buffer             = Array();

        foreach($requestQueueData as $current) {
            $start          = microtime(true);

            $requestObj     = new RequestQueue($current);

            $charDetails    = Character::get(Array('id' => $current['charid']));
            $charObj        = new Character($charDetails);

            $apiKeyDetails  = ApiKey::get(Array('id' => $charObj->getDetail('apikey')));
            $apiKeyObj      = new ApiKey($apiKeyDetails);

            $pheal          = new Pheal\Pheal($apiKeyObj->getDetail('keyId'), $apiKeyObj->getDetail('vCode'), 'char');
            $phealColonies  = $pheal->PlanetaryColonies(Array("characterID" => $charDetails['charid']));
            ++$nbQueries;

            $coloniesData   = Array();

            foreach($phealColonies->colonies as $planetary) {
                $planetID   = (string) $planetary['planetID'];
                $planetName = (string) $planetary['planetName'];
                $planetType = (string) $planetary['planetTypeName'];

                $coloniesData[$planetID]    = Array(
                    'id'                    => $planetID,
                    'name'                  => $planetType. ' - ' .$planetName,
                    'extractors'            => Array(),
                    'launchpad'             => Array(),
                    'launchpadRemaining'    => 10000,
                    'storage'               => Array()
                );

                $phealPins = $pheal->PlanetaryPins(Array(
                    "characterID"   => $charDetails['charid'],
                    "planetID"      => $planetID
                ));
                ++$nbQueries;

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
                        $item           = EveItem::get(Array('typeID' => $contentTypeID));

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

            // check nbQueries & time
            // if we've same or more queries than we can do in one second, we just wait until next second ...
            if($nbQueries >= $requestPerSeconds) {
                $end        = microtime(true);
                $diff       = $end - $start;
                $remaining  = 1 - $diff;
                $micro      = (int) ($remaining * 1000000);

                // wait now :)
                usleep($micro);
            }
        }

        sleep(1);
    }

