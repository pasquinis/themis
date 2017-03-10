<?php

namespace Themis\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Themis\Application;

class ViewTransactionsController
{
    public function doGetTransactions($transactionId, Request $request, Application $application)
    {
        $sql = 'SELECT * FROM transactions WHERE id = ?';
        $transaction = $application['db']->fetchAssoc($sql, [(int) $transactionId]);

        $sql = 'SELECT * FROM underscore WHERE id = ?';
        $underscore = empty($application['db']->fetchAssoc($sql, [(int) $transactionId]));

        $class = ($underscore === TRUE) ? 'btn btn-success btn-sm' : 'btn btn-info btn-sm table-active';

        return $application['twig']->render('transaction.twig', array(
            'transaction' => $transaction,
            'underscore' => $underscore,
            'class' => $class
        ));
    }
}
