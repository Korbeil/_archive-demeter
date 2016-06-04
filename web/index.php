<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 06/05/15
 * Time: 00:21
 */
    session_start();

    $loader = require_once __DIR__. '/../vendor/autoload.php';
    $loader->add('Demeter', __DIR__.'/../src/');

    $app    = new Demeter\Core\Application();
    $app->routine();
