<?php

namespace Themis\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Themis\Application;

class ViewBlankController
{
    public function doGetBlank(Request $request, Application $application)
    {
        return Response::create('', 404);
    }
}
