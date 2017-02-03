<?php

namespace Themis\Controller\Provider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class Hello implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $home = $app['controllers_factory'];
        $home->get('/', 'Themis\\Controller\\HelloController::show');
        return $home;
    }
}
