<?php
namespace Themis\Projection;

use Themis\Application;
use Doctrine\DBAL\Schema\Table;
use Silex\Provider\DoctrineServiceProvider;
use Silex\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HouseholdBudgetTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->dbSetup();
        $this->expenditureItems = new CategoryExpenditureItems();
        $this->budget = new HouseholdBudget($this->app, $this->expenditureItems);
    }

    public function createApplication()
    {
        $app = new Application();
        $app = $this->debug($app);
        $app->register(new DoctrineServiceProvider(), [
            'db.options' => [
                'driver'   => 'pdo_sqlite',
            ],
        ]);
        return $app;
    }

    public function testShouldCreateOneRowWhenThereIsOnlyOneTransaction()
    {
        $this->prepareTransactions();
        $expected = [
            "operationdate" => "10/01/2011",
            "category" => "Altro",
            "revenue" => "0",
            "expenditure" => "-10",
            "correlation" => "transaction/1",
        ];

        $this->assertTupleEquals(0);

        $this->budget->handle(
            $startDate = '01/01/2011',
            $endDate = '31/01/2011'
        );

        $this->assertTupleExpect([$expected]);
    }

    private function assertTupleEquals($expected)
    {
        $sql = 'SELECT * FROM project_household_budget';
        $actual = count($this->app['db']->fetchAll($sql));
        if ( $actual != $expected) {
            $this->fail("Found {$actual} elements but was expected {$expected}");
        }
    }

    private function assertTupleExpect($passed)
    {
        $sql = <<<SQL
SELECT operationdate,
    category,
    revenue,
    expenditure,
    correlation
    FROM project_household_budget
SQL;
        $actual = json_encode($this->app['db']->fetchAll($sql));
        $expected = json_encode($passed);
        if ($actual !== $expected) {
            $this->fail("Found {$actual} elements but was expected {$expected}");
        }
    }

    private function prepareTransactions()
    {
        $payload = [
            'operationDate' => '10/01/2011',
            'valueDate' => '10/01/2011',
            'description' => 'PAGAMENTO TRAMITE POS',
            'reason' => 'POS CARTA 124567 DEL 10/01/2011 ORE 20:44 C/O 1234567890 PINCO PALLO',
            'revenue' => 0,
            'expenditure' => -10.00,
            'currency' => 'EUR',
        ];

        $this->app['db']->insert(
            'transactions',
             $payload
        );
    }

    private function dbSetup()
    {
        $transactions = new Table('transactions');
        $transactions->addColumn(
            'id',
            'integer',
            ['unsigned' => true, 'autoincrement' => true]
        );
        $transactions->addColumn(
            'operationdate',
            'text'
        );
        $transactions->addColumn(
            'valuedate',
            'text'
        );
        $transactions->addColumn(
            'description',
            'text'
        );
        $transactions->addColumn(
            'reason',
            'text'
        );
        $transactions->addColumn(
            'revenue',
            'integer'
        );
        $transactions->addColumn(
            'expenditure',
            'integer'
        );
        $transactions->addColumn(
            'currency',
            'text'
        );
        $transactions->setPrimaryKey(['id']);

        $project_household_budget = new Table('project_household_budget');
        $project_household_budget->addColumn(
            'operationdate',
            'text'
        );
        $project_household_budget->addColumn(
            'category',
            'text'
        );
        $project_household_budget->addColumn(
            'revenue',
            'integer'
        );
        $project_household_budget->addColumn(
            'expenditure',
            'integer'
        );
        $project_household_budget->addColumn(
            'correlation',
            'text'
        );
        $project_household_budget->addColumn(
            'projected_at',
            'text'
        );

        $schema = $this->app['db']->getSchemaManager();
        $schema->createTable($transactions);
        $schema->createTable($project_household_budget);
    }

    private function debug(Application $app)
    {
        $app['debug'] = true;
        unset($app['exception_handler']);
        return $app;
    }
}
