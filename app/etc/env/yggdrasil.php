<?php

return [
    'app' => [
        'environment' => 'yggdrasil',
        'responseMode' => 'view',
        'modules' => [
            'admin' => 'Admin' ],
        'assetVersion' => 26,
        'assetMode' => 'production' ],

    'cache' => [
        'adapter' => 'redis' ],

    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '##SQLPASSWORD##',
        'dbname' => 'silentbarn',
        'persistent' => TRUE ],

    'paths' => [
        'baseUri' => 'http://silentbarn.org/',
        'assetUri' => 'http://silentbarn.org/assets/%s/',
        'hostname' => 'silentbarn.org',
        'media' => '/home/mike/www/barn.shadowmere.net/www-data/media',
        'mediaPublic' => 'http://silentbarn.org/media/' ],

    'cookies' => [
        'secure' => FALSE ],

    'profiling' => [
        'query' => FALSE ],

    'instagram' => [
        'clientId' => '##INSTAGRAMCLIENTID##' ],

    'mailgun' => [
        'to' => [
            'rentals' => '##MAILGUNTORENTALS##',
            'events' => '##MAILGUNTOEVENTS##',
            'stewdios' => '##MAILGUNTOSTEWDIOS##' ],
        'smtp' => [
            'hostname' => '##MAILGUNHOSTNAME##',
            'username' => '##MAILGUNUSERNAME##',
            'password' => '##MAILGUNPASSWORD##' ]
    ]];