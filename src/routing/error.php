<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 11/05/15
 * Time: 00:42
 */

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