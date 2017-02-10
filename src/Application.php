<?php

namespace Themis;

use Themis\Controller\Provider\Hello;
use Themis\Controller\Provider\Transactions;

class Application extends \Silex\Application
{
    public function __construct()
    {
        parent::__construct();

        $this->mount('/hello/{name}', new Hello());
        $this->mount('/transactions', new Transactions());
    }
}
