<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;

$app->mount('/hello/{name}', new \Themis\Controller\Provider\Hello());

$app->run();
