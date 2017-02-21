<?php

namespace Themis\Controller\Provider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class Transactions implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $home = $app['controllers_factory'];
        $home->post('/', 'Themis\\Controller\\TransactionsController::doPostTransactions');
        $home->get('/{transactionId}', 'Themis\\Controller\\TransactionsController::doGetTransactions');
        return $home;
    }
}
