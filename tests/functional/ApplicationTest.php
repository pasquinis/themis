<?php

namespace Themis;

use Silex\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Silex\Provider\DoctrineServiceProvider;
use Doctrine\DBAL\Schema\Table;

class ApplicationTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->dbSetup();
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

    /**
     * @expectedException Exception
     */
    public function testShouldReturn404WhenICallHomeRoot()
    {
        $client = $this->createClient();
        $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isNotFound());
    }

    public function testShouldReturnTheCorrectContentWhenICallRouteHelloWithSimoneAsParameter()
    {
        $client = $this->createClient();
        $client->request('GET', '/hello/simone/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('Hello simone', $client->getResponse()->getContent());
    }

    public function testShouldCreateANewTransactions()
    {
        $client = $this->createClient();
        $postParameters = [
            'operationDate' => '09/02/2017',
            'valueDate' => '09/02/2017',
            'description' => 'PAGAMENTO TRAMITE POS',
            'reason' => 'POS CARTA 124567 DEL 09/02/2017 ORE 13:44 C/O 1234567890 PINCO PALLO',
            'revenue' => 0,
            'expenditure' => -18.11,
            'currency' => 'EUR',
        ];
        $client->request('POST', '/api/transactions/', $postParameters);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/api/transactions/1', $client->getResponse()->headers->get('Location'));
    }

    public function testShouldPreviewASingleTransaction()
    {
        $this->markTestIncomplete();
        $client = $this->createClient();
        $postParameters = [
            'operationDate' => '09/02/2017',
            'valueDate' => '09/02/2017',
            'description' => 'PAGAMENTO TRAMITE POS',
            'reason' => 'POS CARTA 124567 DEL 09/02/2017 ORE 13:44 C/O 1234567890 PINCO PALLO',
            'revenue' => 0,
            'expenditure' => -18.11,
            'currency' => 'EUR',
        ];
        $client->request('POST', '/api/transactions/', $postParameters);
        $client = $this->createClient();
        $client->request('GET', '/transactions/1');
        $this->assertContains('PAGAMENTO', $client->getResponse()->getContent());
    }

    public function testTheIdempotencyOfATransactionCreation()
    {
        $client = $this->createClient();
        $postParameters = [
            'operationDate' => '09/02/2017',
            'valueDate' => '09/02/2017',
            'description' => 'PAGAMENTO TRAMITE POS',
            'reason' => 'POS CARTA 124567 DEL 09/02/2017 ORE 13:44 C/O 1234567890 PINCO PALLO',
            'revenue' => 0,
            'expenditure' => -18.11,
            'currency' => 'EUR',
        ];
        $client->request('POST', '/api/transactions/', $postParameters);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/api/transactions/1', $client->getResponse()->headers->get('Location'));
        $postParameters = [
            'operationDate' => '09/02/2017',
            'valueDate' => '09/02/2017',
            'description' => 'PAGAMENTO TRAMITE POS',
            'reason' => 'POS CARTA 124567 DEL 09/02/2017 ORE 13:44 C/O 1234567890 PINCO PALLO',
            'revenue' => 0,
            'expenditure' => -18.11,
            'currency' => 'EUR',
        ];
        $client = $this->createClient();
        $client->request('POST', '/api/transactions/', $postParameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/api/transactions/1', $client->getResponse()->headers->get('Location'));
    }

    public function testShouldCreateTwoTransactions()
    {
        $client = $this->createClient();
        $postParameters = [
            'operationDate' => '09/02/2017',
            'valueDate' => '09/02/2017',
            'description' => 'PAGAMENTO TRAMITE POS',
            'reason' => 'POS CARTA 124567 DEL 09/02/2017 ORE 13:44 C/O 1234567890 PINCO PALLO',
            'revenue' => 0,
            'expenditure' => -18.11,
            'currency' => 'EUR',
        ];
        $client->request('POST', '/api/transactions/', $postParameters);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/api/transactions/1', $client->getResponse()->headers->get('Location'));
        $postParameters = [
            'operationDate' => '10/02/2017',
            'valueDate' => '10/02/2017',
            'description' => 'PAGAMENTO TRAMITE POS',
            'reason' => 'POS CARTA 124567 DEL 10/02/2017 ORE 20:44 C/O 1234567890 PINCO PALLO',
            'revenue' => 0,
            'expenditure' => -10.00,
            'currency' => 'EUR',
        ];
        $client = $this->createClient();
        $client->request('POST', '/api/transactions/', $postParameters);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/api/transactions/2', $client->getResponse()->headers->get('Location'));
    }

    public function testShouldReadASelectedTransaction()
    {
        $values = [
            'operationdate' => '17/09/2011',
            'valuedate' => '17/09/2011',
            'description' => 'PAGAMENTO TRAMITE POS',
            'reason' => 'POS CARTA 124567 DEL 17/09/2011 ORE 13:44 C/O 1234567890 PINCO PALLO',
            'revenue' => 'NULL',
            'expenditure' => '-18.11',
            'currency' => 'EUR',
        ];
        $this->app['db']->insert('transactions', $values);

        $client = $this->createClient();
        $client->request('GET', '/api/transactions/1');

        $expectedResponse = json_encode(
           ['id' => '1'] + $values
        );
        $this->assertIsJsonResponse($client->getResponse());
        $this->assertEquals($expectedResponse, $client->getResponse()->getContent());
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
        $schema = $this->app['db']->getSchemaManager();
        $schema->createTable($transactions);
    }

    private function assertIsJsonResponse($response)
    {
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(
            'application/json',
            $response->headers->get('Content-Type')
        );
    }

    private function debug(Application $app)
    {
        $app['debug'] = true;
        unset($app['exception_handler']);
        return $app;
    }
}
