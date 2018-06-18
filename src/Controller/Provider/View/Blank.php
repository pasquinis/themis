<?php

namespace Themis\Controller\Provider\View;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class Blank implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $home = $app['controllers_factory'];
        $home->get(
            '/',
            'Themis\\Controller\\ViewBlankController::doGetBlank'
        );
        return $home;
    }
}
