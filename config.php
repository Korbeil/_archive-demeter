<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 09/05/15
 * Time: 02:03
 */

    // Database conf
    GlobalVars::getInstance()->set('database', array(
        'dsn'   => 'mysql:dbname=demeter;host=localhost',
        'user'  => 'demeter',
        'pass'  => 'cw824jHM'
    ));

    // Eve-SSO
    GlobalVars::getInstance()->set('eve-sso', array(
        'base_url'      => 'https://login.eveonline.com',
        'auth_url'      => '/oauth/authorize',
        'token_url'     => '/oauth/token',
        'verify_url'    => '/oauth/verify',
        'client_id'     => 'be4be038530047ae8d61e54b018d5950',
        'secret_key'    => 'CNUEE5OGQFprRBxHvaAg2Jqp1Q7QqBEtWsNfhDek',
        'callback'      => 'http://naglfar.fail/eve-sso'
    ));

    // set some caching for PhealNG =)
    \Pheal\Core\Config::getInstance()->cache = new \Pheal\Cache\RedisStorage();