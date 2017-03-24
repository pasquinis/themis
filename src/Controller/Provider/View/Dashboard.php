<?php

namespace Themis\Controller\Provider\View;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class Dashboard implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $home = $app['controllers_factory'];
        $home->get(
            '/',
            'Themis\\Controller\\ViewDashboardController::doGetDashboard'
        );
        return $home;
    }
}
