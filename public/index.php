<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Themis\Application();

$app['debug'] = true;

$app->run();
