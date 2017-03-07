<?php

namespace Themis\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Themis\Application;

class TransactionsController
{
    public function doPostTransactions(Request $request, Application $application)
    {
        $payload = $request->request->all();

        if ($this->isAlreadySavedTheTransaction($payload, $application)) {
            $response = new Response();
            $location = $this->createLocation($request, $application, $payload);
            $response->headers->set('Location', $location);
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

        $application['db']->insert(
            'transactions',
             $payload
        );

        $transaction = $this->transactionWith($payload, $application);

        $response = new Response();
        $location = $this->createLocation($request, $application, $payload);
        $response->headers->set('Location', $location);
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

    private function createLocation(Request $request, Application $application, array $payload)
    {
        return 'http://' . $request->getHost() . '/api/transactions/' . $this->getTransactionId($payload, $application);
    }

    private function isAlreadySavedTheTransaction(array $payload, Application $application)
    {
        return !empty($this->transactionWith($payload, $application));
    }

    private function getTransactionId(array $payload, Application $application)
    {
        $obj = $this->transactionWith($payload, $application);
        return $obj['id'];
    }

    private function transactionWith(array $payload, Application $application)
    {
        $sql = 'SELECT * FROM transactions WHERE description = ? AND reason = ?';
        return $application['db']->fetchAssoc($sql, [$payload['description'], $payload['reason']]);
    }
}
