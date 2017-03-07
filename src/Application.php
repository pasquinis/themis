<?php

namespace Themis;

use Themis\Controller\Provider\Hello;
use Themis\Controller\Provider\Transactions;
use \Silex\Provider\DoctrineServiceProvider;
use \Silex\Provider\TwigServiceProvider;

class Application extends \Silex\Application
{
    public function __construct()
    {
        parent::__construct();

        $this->register(new DoctrineServiceProvider(), [
            'db.options' => [
                'driver'   => 'pdo_sqlite',
                'path'     => __DIR__.'/../app.db',// TODO decide the location of app.db
            ],
        ]);
        $this->register(new TwigServiceProvider(), [
            'twig.path' => __DIR__.'/views',
        ]);

        $this->mount('/hello/{name}', new Hello());
        $this->mount('/api/transactions', new Transactions());
    }
}
