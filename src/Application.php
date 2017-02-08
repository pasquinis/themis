<?php

namespace Themis;

use Themis\Controller\Provider\Hello;

class Application extends \Silex\Application
{
    public function __construct()
    {
        parent::__construct();

        $this->mount('/hello/{name}', new Hello());
    }
}
