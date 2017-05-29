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
        //TODO eseguire projection
        $this->project($transactions);
        //TODO salvare i dati calcolati nella tabella project
    }


    private function project($transactions)
    {
        foreach ($transactions as $transaction) {
            //TODO ciclare chiamando la funzione che raggruppa per poi inserire nella tabella payment_
            $prepareTransaction = $this->prepareTransaction($transaction);
            $this->add($prepareTransaction);
        }
    }

    private function selectDataFromTransactions($startDate, $endDate)
    {
        $sql = 'SELECT * FROM transactions';
        return $this->app['db']->fetchAll($sql);
    }

    private function prepareTransaction($transaction)
    {
        $id = $transaction['id'];
        $now = (new DateTime('now'))->format(DateTime::ISO8601);
        $category = $this->expenditureItems->category($transaction);

        $transaction['category'] = $category;
        $transaction['revenue'] = 0; //TODO fake
        $transaction['expenditure'] = 0; //TODO fake
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
