<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 08/03/15
 * Time: 22:44
 */
    session_start();

    require_once __DIR__. '/data.php';

    require_once __DIR__. '/vendor/autoload.php';

    $loader = new Twig_Loader_Filesystem(__DIR__. '/views');
    $twig   = new Twig_Environment($loader);

    function request_api($method, $keys, $more = Array()) {
        $addition = '';
        foreach($more as $key => $value) {
            $addition .= '&' .$key. '=' .$value;
        }

        $api_url = 'https://api.eveonline.com/' .$method. '.xml.aspx?keyID=' .$keys[0]. '&vCode=' .$keys[1]. $addition;
        $content = file_get_contents($api_url);
        $return = simplexml_load_string($content);

        return $return->result->rowset;
    }

    if(isset($_SESSION['user'])) {
        $api_keys   = $array[$_SESSION['user']];
        $allDatas   = Array();

        foreach($api_keys as $keys) {
            // char data
            $result = request_api('account/Characters', $keys);
            $charID = (string) $result->row['characterID'];

            $allDatas[$charID]                          = Array();
            $allDatas[$charID]['character']['id']       = (string) $result->row['characterID'];
            $allDatas[$charID]['character']['name']     = (string) $result->row['name'];
            $allDatas[$charID]['character']['corpid']   = (string) $result->row['corporationID'];
            $allDatas[$charID]['character']['corp']     = (string) $result->row['corporationName'];

            // planetary data
            $result                                     = request_api('char/PlanetaryColonies', $keys, array('characterID' => $charID));
            $allDatas[$charID]['planets']               = Array();

            foreach($result->row as $planet) {
                $planetID                                               = (string) $planet['planetID'];
                $allDatas[$charID]['planets'][$planetID]                = Array();
                $allDatas[$charID]['planets'][$planetID]['id']          = $planetID;
                $allDatas[$charID]['planets'][$planetID]['extractors']  = Array();

                $allDatas[$charID]['planets'][$planetID]['launchpadRemaining']  = 10000;
                $allDatas[$charID]['planets'][$planetID]['launchpad']           = Array();

                $planetResult = request_api('char/PlanetaryPins', $keys, array('characterID' => $charID, 'planetID' => $planetID));

                $expireTimers = Array();
                $storageQt    = 0;

                foreach($planetResult->row as $item) {

                    switch($items_type[(string) $item['typeID']]['type']) {
                        case 'extractor':
                            $dateInstall    = DateTime::createFromFormat('Y-m-d H:i:s', $item['installTime']);
                            $dateExpiry     = DateTime::createFromFormat('Y-m-d H:i:s', $item['expiryTime']);

                            $allDatas[$charID]['planets'][$planetID]['extractors'][(string) $item['pinID']]['id']       = (string) $item['pinID'];
                            $allDatas[$charID]['planets'][$planetID]['extractors'][(string) $item['pinID']]['install']  = $dateInstall;
                            $allDatas[$charID]['planets'][$planetID]['extractors'][(string) $item['pinID']]['expiry']   = $dateExpiry;
                            $allDatas[$charID]['planets'][$planetID]['extractors'][(string) $item['pinID']]['elapsed']  = round((1 - ((time() - $dateExpiry->getTimestamp()) / ($dateInstall->getTimestamp() - $dateExpiry->getTimestamp()))) * 100, 0);
                            $allDatas[$charID]['planets'][$planetID]['type']                                            = $items_type[(string) $item['typeID']]['planet'];

                            $expireTimers[] = $allDatas[$charID]['planets'][$planetID]['extractors'][(string) $item['pinID']]['elapsed'];
                            break;

                        case 'launchpad':

                            $item_mass = $item['contentQuantity'] * $items_info[(string) $item['contentTypeID']]['mass'];

                            $allDatas[$charID]['planets'][$planetID]['launchpad'][(string) $item['contentTypeID']] = Array(
                                'id'        => (string) $item['contentTypeID'],
                                'name'      => $items_info[(string) $item['contentTypeID']]['name'],
                                'quantity'  => (string) $item['contentQuantity'],
                                'tech'      => $items_info[(string) $item['contentTypeID']]['tech'],
                                'mass'      => $item_mass,
                                'color'     => $items_info[(string) $item['contentTypeID']]['color']
                            );

                            $allDatas[$charID]['planets'][$planetID]['launchpadRemaining'] -= $item_mass;

                            break;
                    }
                }
                $maxExpireTimer = max($expireTimers);

                if($maxExpireTimer > 90) {
                    $allDatas[$charID]['character']['heading'] = 'panel-danger';
                } else if($maxExpireTimer > 70) {
                    $allDatas[$charID]['character']['heading'] = 'panel-warning';
                } else {
                    $allDatas[$charID]['character']['heading'] = 'panel-success';
                }

                $allDatas[$charID]['character']['planetNb']                 = count($allDatas[$charID]['planets']);
                $allDatas[$charID]['planets'][$planetID]['maxExpire']       = $maxExpireTimer;

                $allDatas[$charID]['planets'][$planetID]['level']           = (string) $planet['upgradeLevel'];
                $allDatas[$charID]['planets'][$planetID]['buildings']       = (string) $planet['numberOfPins'];
                $allDatas[$charID]['planets'][$planetID]['solar_system']    = (string) $planet['solarSystemName'];
                $allDatas[$charID]['planets'][$planetID]['name']            = (string) $planet['planetName'];
            }
        }

        /*
        echo '<pre>';
        print_r($allDatas);
        echo '</pre>';
        */

        echo $twig->render('data.twig', Array('data' => $allDatas, 'items' => $items_info, 'time' => time()));
    } else if(isset($_POST['inputPassword']) && array_key_exists($_POST['inputPassword'], $array)) {
        $_SESSION['user'] = $_POST['inputPassword'];
        echo 'Reload ...';
        header('Location: '.$_SERVER['REQUEST_URI']);
    } else {
        echo $twig->render('login.twig');
    }