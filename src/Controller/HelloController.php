<?php

namespace Themis\Controller;

use Themis\Application;

class HelloController
{
    public function show($name, Application $application)
    {
        // return "Hello I am {$name}";
        return $application['twig']->render('hello.twig', array(
            'name' => $name,
        ));
    }
}
