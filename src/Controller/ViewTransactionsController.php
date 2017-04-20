<?php

namespace Themis\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Themis\Application;

class ViewTransactionsController
{
    const FIRST_DAY_OF_MONTH = '01';
    const LAST_DAY_OF_MONTH = '31';

    public function doGetTransactions($transactionId, Request $request, Application $application)
    {
        $sql = 'SELECT * FROM transactions WHERE id = ?';
        $transaction = $application['db']->fetchAssoc($sql, [(int) $transactionId]);

        $underscore = $this->toUnderscore($transactionId, $application);

        $class = $this->cssClassDefinition($underscore);

        return $application['twig']->render('transaction.twig', array(
            'transaction' => $transaction,
            'underscore' => $underscore,
            'class' => $class
        ));
    }

    public function doGetTransactionsByYearMonth($year, $month, Request $request, Application $application)
    {
        $leftDate = "{$year}-{$month}-" . self::FIRST_DAY_OF_MONTH;
        $rightDate = "{$year}-{$month}-" . self::LAST_DAY_OF_MONTH;

        $sql = "SELECT * FROM transactions WHERE valuedate BETWEEN '{$leftDate}' AND '{$rightDate}'";
        $transactions = $application['db']->fetchAll($sql);

        $toPreview = [];
        foreach($transactions as $transaction) {
            $underscore = $this->toUnderscore($transaction['id'], $application);
            $toPreview[] = [
                'transaction' => $transaction,
                'underscore' => $underscore,
                'class' => $this->cssClassDefinition($underscore),
                'tablerow' => $this->cssTrDefinition($underscore),
            ];
        }

        return $application['twig']->render('transactions-year-month.twig', array(
            'transactions' => $toPreview,
        ));
    }

    public function doGetTransactionsByYear($year, Request $request, Application $application)
    {
        $leftDate = "{$year}-01-" . self::FIRST_DAY_OF_MONTH;
        $rightDate = "{$year}-12-" . self::LAST_DAY_OF_MONTH;

        $sql = "SELECT * FROM transactions WHERE valuedate BETWEEN '{$leftDate}' AND '{$rightDate}'";
        $transactions = $application['db']->fetchAll($sql);

        $toPreview = [];
        foreach($transactions as $transaction) {
            $underscore = $this->toUnderscore($transaction['id'], $application);
            $toPreview[] = [
                'transaction' => $transaction,
                'underscore' => $underscore,
                'class' => $this->cssClassDefinition($underscore),
            ];
        }

        return $application['twig']->render('transactions-year-month.twig', array(
            'transactions' => $toPreview,
        ));
    }

    private function toUnderscore($transactionId, Application $application)
    {
        $sql = 'SELECT * FROM underscore WHERE id = ?';
        return empty($application['db']->fetchAssoc($sql, [(int) $transactionId]));
    }

    private function cssClassDefinition($underscore)
    {
        return ($underscore === TRUE) ? 'btn btn-success btn-sm' : 'btn btn-info btn-sm table-active';
    }

    private function cssTrDefinition($underscore)
    {
        return ($underscore === TRUE) ? 'table-active' : 'none';
    }
}
