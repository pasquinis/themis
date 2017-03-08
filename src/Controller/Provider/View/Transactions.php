<?php

namespace Themis\Controller\Provider\View;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class Transactions implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $home = $app['controllers_factory'];
        $home->get('/{transactionId}', 'Themis\\Controller\\ViewTransactionsController::doGetTransactions');
        return $home;
    }
}
