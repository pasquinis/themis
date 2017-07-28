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
        $totalEvents = count($transactions);
        $this->project($transactions);
        return $totalEvents;
    }

    private function project($transactions)
    {
        foreach ($transactions as $transaction) {
            $prepareTransaction = $this->prepareTransaction($transaction);
            if ($this->isInsert($transaction)) {
                $this->add($prepareTransaction);
                //TODO enable for debug
                // var_dump("I: ".var_export($prepareTransaction,true));
            } else {
                $this->update($prepareTransaction);
                //TODO enable for debug
                // var_dump("U: ".var_export($prepareTransaction,true));
            }
        }
    }

    private function selectDataFromTransactions($startDate, $endDate)
    {
        $sql = "SELECT * FROM transactions WHERE valuedate BETWEEN '{$startDate}' AND '{$endDate}'";
        return $this->app['db']->fetchAll($sql);
    }

    private function isInsert($transaction)
    {
        $id = $transaction['id'];
        $sql = "SELECT * FROM project_household_budget WHERE correlation == 'transaction/{$id}'";
        $count = count($this->app['db']->fetchAll($sql));
        if ($count > 1) {
            //TODO handle with specific error
            throw \Exception('ERROR: there are more then one row with the same correlation');
        }

        return $count == 0;
    }

    private function prepareTransaction($transaction)
    {
        $id = $transaction['id'];
        $now = (new DateTime('now'))->format(DateTime::ISO8601);

        try {
            $category = $this->expenditureItems->category($transaction);
        } catch (\Exception $e) {
            var_dump(var_export($transaction, true));
            throw $e;
        }

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

    private function update($transaction)
    {
        $this->app['db']->update(
            self::TABLE,
            [
                'operationdate' => $transaction['operationdate'],
                'category' => $transaction['category'],
                'revenue' => $transaction['revenue'],
                'expenditure' => $transaction['expenditure'],
                'projected_at' => $transaction['projected_at'],
            ],
            [
                'correlation' => $transaction['correlation'],
            ]
        );
    }
}
