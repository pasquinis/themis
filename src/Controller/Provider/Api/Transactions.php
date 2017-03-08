<?php

namespace Themis\Controller\Provider\Api;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class Transactions implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $home = $app['controllers_factory'];
        $home->post('/', 'Themis\\Controller\\ApiTransactionsController::doPostTransactions');
        $home->get('/{transactionId}', 'Themis\\Controller\\ApiTransactionsController::doGetTransactions');
        $home->put('/{transactionId}', 'Themis\\Controller\\ApiTransactionsController::doPutTransactions');
        return $home;
    }
}
