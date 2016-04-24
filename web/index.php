<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 06/05/15
 * Time: 00:21
 */
    session_start();

    $loader = require_once __DIR__. '/../vendor/autoload.php';
    $app    = new Silex\Application();

    require_once __DIR__. '/../src/app.php';

    $app->run();
