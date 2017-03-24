<?php

namespace Themis\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Themis\Application;

class ViewDashboardController
{
    public function doGetDashboard(Request $request, Application $application)
    {
        $openTransactionsQueryCount = <<<SQL
        SELECT COUNT(*) AS tot
        FROM transactions
        LEFT OUTER JOIN underscore ON transactions.id = underscore.id
        WHERE underscore.id IS NULL
SQL;
        $openTransactionsCount = $application['db']->fetchAssoc($openTransactionsQueryCount)['tot'];

        $openTransactionsQuery= <<<SQL
        SELECT operationdate,count(*) AS tot
        FROM transactions
        LEFT OUTER JOIN underscore ON transactions.id = underscore.id
        WHERE underscore.id IS NULL
        GROUP BY operationdate
        ORDER BY transactions.operationdate ASC
SQL;
        $openTransactions= $application['db']->fetchAll($openTransactionsQuery);

        return $application['twig']->render(
            'dashboard.twig',
            [
                'openTransactionsCount' => $openTransactionsCount,
                'openTransactions' => $openTransactions,
            ]
        );
    }
}
