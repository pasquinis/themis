<?php

namespace Themis\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Themis\Http\ResponseFactory;
use Themis\Application;
use Themis\Payload\Request as PayloadRequest;
use Themis\Payload\RequestCariparma;
use Themis\Transaction\Transaction;
use \DateTime;

class ApiTransactionsController
{
    public function doPostTransactionsForIntesa(Request $request, Application $application)
    {
        try {
            $payloadRequest = PayloadRequest::box($request);
        } catch ( BadRequestHttpException $e) {
            return ResponseFactory::unprocessable();
        }

        $transaction = Transaction::byRequest($payloadRequest);
        $payload = $transaction->toArray();

        if ($this->isAlreadySavedTheTransaction($payload, $application)) {
            return ResponseFactory::ok([
                'location' => $this->createLocation($request, $application, $payload)
            ]);
        }

        $application['db']->insert(
            'transactions',
             $payload
        );

        $transaction = $this->transactionWith($payload, $application);

        return ResponseFactory::created([
            'location' => $this->createLocation($request, $application, $payload)
        ]);
    }

    public function doPostTransactions(Request $request, Application $application)
    {
        try {
            $payloadRequest = RequestCariparma::box($request);
        } catch ( BadRequestHttpException $e) {
            var_dump($e->getMessage());
            return ResponseFactory::unprocessable();
        }

        $transaction = Transaction::byRequestCariparma($payloadRequest);
        $payload = $transaction->toArray();

        if ($this->isAlreadySavedTheTransaction($payload, $application)) {
            return ResponseFactory::ok([
                'location' => $this->createLocation($request, $application, $payload)
            ]);
        }

        $application['db']->insert(
            'transactions',
             $payload
        );

        $transaction = $this->transactionWith($payload, $application);

        return ResponseFactory::created([
            'location' => $this->createLocation($request, $application, $payload)
        ]);
    }

    private function forgePayload(array $payload)
    {
        $forging = function ($date) {
            $dateSplitted = explode('/', $date);
            return "{$dateSplitted[2]}-{$dateSplitted[1]}-{$dateSplitted[0]}";
        };

        if (isset($payload['data'])) {
            $parsed = str_getcsv($payload['data']);
            $forged = [
                'operationDate' => $forging($parsed[0]),
                'valueDate' => $forging($parsed[1]),
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

        return ResponseFactory::okInJsonOutput(['content' => $transaction]);
    }

    public function doPutTransactions($transactionId, Request $request, Application $application)
    {
        if (!$this->existTheTransaction($transactionId, $application)) {
            return ResponseFactory::badRequest();
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
            return ResponseFactory::ok();
        } else {
            $application['db']->delete(
                'underscore',
                [
                    'id' => $transactionId
                ]
            );
            return ResponseFactory::ok();
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
        $sql = 'SELECT * FROM transactions WHERE description = ? AND reason = ? AND operationdate = ?';
        return $application['db']
            ->fetchAssoc(
                $sql,
                [
                    $payload['description'],
                    $payload['reason'],
                    $payload['operationDate']
                ]
        );
    }

    private function transactionById($transactionId, Application $application)
    {
        $sql = 'SELECT * FROM transactions WHERE id = ?';
        return $application['db']->fetchAssoc($sql, [$transactionId]);
    }
}
