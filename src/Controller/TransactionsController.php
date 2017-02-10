<?php

namespace Themis\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionsController
{
    public function doPostTransactions(Request $request)
    {
        //$request->request->all();
        $response = new Response();
        $response->setStatusCode(Response::HTTP_CREATED);
        return $response;
    }
}
