<?php
namespace Themis\Projection;

use Themis\Application;
use \DateTime;

class HouseholdBudget
{
    const TABLE = 'project_household_budget';

    private $app;
    private $expenditureItems;

    public function __construct(Application $app, ExpenditureItems $expenditureItems)
    {
        $this->app = $app;
        $this->expenditureItems = $expenditureItems;
    }

    public function handle($startDate, $endDate)
    {
        $transactions = $this->selectDataFromTransactions($startDate, $endDate);
        $this->project($transactions);
    }

    private function project($transactions)
    {
        foreach ($transactions as $transaction) {
            $prepareTransaction = $this->prepareTransaction($transaction);
            $this->add($prepareTransaction);
        }
    }

    private function selectDataFromTransactions($startDate, $endDate)
    {
        $startDate = '2011-01-01';
        $endDate = '2011-01-31';
        $sql = "SELECT * FROM transactions WHERE valuedate BETWEEN '{$startDate}' AND '{$endDate}'";
        return $this->app['db']->fetchAll($sql);
    }

    private function prepareTransaction($transaction)
    {
        $id = $transaction['id'];
        $now = (new DateTime('now'))->format(DateTime::ISO8601);
        $category = $this->expenditureItems->category($transaction);

        $transaction['category'] = $category;
        $transaction['revenue'] = $transaction['revenue'];
        $transaction['expenditure'] = $transaction['expenditure'];
        $transaction['correlation'] = "transaction/{$id}";
        $transaction['projected_at'] = $now;
        return $transaction;
    }

    private function add($transaction)
    {
        $this->app['db']->insert(
            self::TABLE,
            [
                'operationdate' => $transaction['operationdate'],
                'category' => $transaction['category'],
                'revenue' => $transaction['revenue'],
                'expenditure' => $transaction['expenditure'],
                'correlation' => $transaction['correlation'],
                'projected_at' => $transaction['projected_at'],
            ]
        );
    }
}
