<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 24/04/16
 * Time: 16:12
 */


    namespace Demeter\Controller;

    class ErrorController extends \Demeter\Core\Controller {

        public static function route(\Silex\Application $app) {
            $app->error(function (\Exception $e, $code) use ($app) {
                if($app['debug'])
                    return;

                switch($code) {
                    case 404:
                        return 404;
                    default:
                        return 'error';
                }
            });
        }

    }
