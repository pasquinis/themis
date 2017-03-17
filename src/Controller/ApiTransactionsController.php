<?php

namespace Themis\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Themis\Application;
use \DateTime;

class ApiTransactionsController
{
    public function doPostTransactions(Request $request, Application $application)
    {
        $payload = $this->forgePayload($request->request->all());

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

    private function forgePayload(array $payload)
    {
        $forging = function ($date) {
            $dateSplitted = explode('/', $date);
            return "{$dateSplitted[2]}-{$dateSplitted[1]}-{$dateSplitted[0]}";
        };

        if (isset($payload['data'])) {
            var_dump($payload['data']);
            $parsed = str_getcsv($payload['data']);
            var_dump($parsed);
            $forged = [
                'operationDate' => $parsed[0],
                'valueDate' => $parsed[1],
                'description' => $parsed[2],
                'reason' => $parsed[3],
                'revenue' => $parsed[4],
                'expenditure' => $parsed[5],
                'currency' => $parsed[6],
            ];
        } else {

            $forged = $payload;
            $forged['operationDate'] = $forging($payload['operationDate']);
            $forged['valueDate'] = $forging($payload['valueDate']);
        }

        return $forged;
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

    public function doPutTransactions($transactionId, Request $request, Application $application)
    {
        if (!$this->existTheTransaction($transactionId, $application)) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
        }

        $payload = $request->request->all();
        $underscore = $payload['underscore'];
        if ($underscore) {
            $application['db']->insert(
                'underscore',
                [
                    'id' => $transactionId,
                    'created' => (new DateTime('now'))->format(DateTime::ISO8601)
                ]
            );
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        } else {
            $application['db']->delete(
                'underscore',
                [
                    'id' => $transactionId
                ]
            );
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    private function createLocation(Request $request, Application $application, array $payload)
    {
        return 'http://' . $request->getHost() . '/api/transactions/' . $this->getTransactionId($payload, $application);
    }

    private function isAlreadySavedTheTransaction(array $payload, Application $application)
    {
        return !empty($this->transactionWith($payload, $application));
    }

    private function existTheTransaction($transactionId, Application $application)
    {
        return !empty($this->transactionById($transactionId, $application));
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

    private function transactionById($transactionId, Application $application)
    {
        $sql = 'SELECT * FROM transactions WHERE id = ?';
        return $application['db']->fetchAssoc($sql, [$transactionId]);
    }
}
