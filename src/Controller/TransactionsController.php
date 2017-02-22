<?php

namespace Themis\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Themis\Application;

class TransactionsController
{
    public function doPostTransactions(Request $request, Application $application)
    {
        $application['db']->insert(
            'transactions',
            $request->request->all()
        );

        $response = new Response();
        $response->headers->set('Location', 'http://localhost');
        $response->setStatusCode(Response::HTTP_CREATED);
        return $response;
    }

    public function doGetTransactions($transactionId, Request $request, Application $application)
    {
        $sql = 'SELECT * FROM transactions WHERE id = ?';
        $transaction = $application['db']->fetchAssoc($sql, [(int) $transactionId]);

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($transaction));
        return $response;
    }
}
