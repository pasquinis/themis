<?php

namespace Themis\Controller\Provider\Api;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class TransactionsForIntesa implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $home = $app['controllers_factory'];
        $home->post('/', 'Themis\\Controller\\ApiTransactionsController::doPostTransactionsForIntesa');
        return $home;
    }
}
