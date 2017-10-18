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

    public function testShouldCreateForBancaIntesaANewTransactionsWithCSVPayload()
    {
        $client = $this->createClient();
        $csvPayload = '26/09/17,26/09/17,Stipendio o pensione,2295.00,,Cod. Disp.: 011709260g42p3 Sala Stipendio O Pensione 01nx02bc1210015064224951640.8040359 Stipendio Settembre 2017 Bonifico A Vostro Favore Disposto Da: Mitt.: DueB Or Benef.: Pinco Pallo';
        $postParameters = [
            'data' => $csvPayload
        ];
        $client->request('POST', '/api/intesa/transactions/', $postParameters);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/api/transactions/1', $client->getResponse()->headers->get('Location'));
        $client = $this->createClient();
        $client->request('GET', 'api/transactions/1');
        $this->assertContains('Stipendio o pensione', $client->getResponse()->getContent());
    }

    public function testShouldBounceForBancaIntesaANewTransactionsWithWrongCSVPayload()
    {
        $client = $this->createClient();
        $wrongCsvPayload = ',Data,Descrizione,Accrediti,Addebiti,Descrizione estesa,';
        $postParameters = [
            'data' => $wrongCsvPayload
        ];
        $client->request('POST', '/api/intesa/transactions/', $postParameters);
        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }

    public function testShouldCreateANewTransactionsWithCSVPayload()
    {
        $client = $this->createClient();
        $csvPayload = '28/02/2017,28/02/2017,PAGAMENTO UTENZE,"SDD A : ILLUMIA SPA FATTURA NUM. 48466/G DEL 08/02/2017, SCADENZA IL 28/02/201 7 ADDEBITO SDD NUMERO 1234567",,-123.80,EUR';
        $postParameters = [
            'data' => $csvPayload
        ];
        $client->request('POST', '/api/transactions/', $postParameters);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/api/transactions/1', $client->getResponse()->headers->get('Location'));
    }

//02/02/2017,01/02/2017,PAGAMENTO TRAMITE POS,POS CARTA 03055510 DEL 01/02/17 ORE 13.41 C/O 321868700233 ACQUA E SAPONE - MILANO NF,,-14.09,EUR

    public function testShouldCreateANewTransactionsWithCSVPayloadWithoutDoubleQuote()
    {
        $client = $this->createClient();
        $csvPayload = '02/02/2017,01/02/2017,PAGAMENTO TRAMITE POS,POS CARTA 03055510 DEL 01/02/17 ORE 13.41 C/O 321868700233 ACQUA E SAPONE - MILANO NF,,-14.09,EUR';
        $postParameters = [
            'data' => $csvPayload
        ];
        $client->request('POST', '/api/transactions/', $postParameters);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/api/transactions/1', $client->getResponse()->headers->get('Location'));
    }
    public function testShouldPreviewASingleTransaction()
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

    public function testShouldPreviewATransactionsForASpecificMonth()
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
        $postParameters = [
            'operationDate' => '10/02/2017',
            'valueDate' => '10/02/2017',
            'description' => 'PAGAMENTO TRAMITE POS',
            'reason' => 'POS CARTA 124567 DEL 10/02/2017 ORE 23:00 C/O 1234567890 PINCO PALLO',
            'revenue' => 0,
            'expenditure' => -8.11,
            'currency' => 'EUR',
        ];
        $client->request('POST', '/api/transactions/', $postParameters);
        $client = $this->createClient();
        $client->request('GET', '/transactions/2017/02');
        $this->assertContains(
            'POS CARTA 124567 DEL 09/02/2017 ORE 13:44 C/O 1234567890 PINCO PALLO',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            'POS CARTA 124567 DEL 10/02/2017 ORE 23:00 C/O 1234567890 PINCO PALLO',
            $client->getResponse()->getContent()
        );
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

    public function testShouldUnderscoreASelectedTransaction()
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
        $client = $this->createClient();
        $putParameters = ['underscore' => true];
        $client->request('PUT', '/api/transactions/1', $putParameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTupleEquals(1);
    }

    public function testShouldReceiveBadRequestWhenIUnderscoreAnInexistentTransaction()
    {
        $client = $this->createClient();
        $putParameters = ['underscore' => false];
        $client->request('PUT', '/api/transactions/1', $putParameters);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertTupleEquals(0);
    }

    public function testShouldUnderscoreAndRemoveUnderscoreFromSelectedTransaction()
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
        $client = $this->createClient();
        $putParameters = ['underscore' => true];
        $client->request('PUT', '/api/transactions/1', $putParameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTupleEquals(1);
        $putParameters = ['underscore' => false];
        $client->request('PUT', '/api/transactions/1', $putParameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTupleEquals(0);
    }

    private function assertTupleEquals($expected)
    {
        $sql = 'SELECT * FROM underscore';
        $actual = count($this->app['db']->fetchAll($sql));
        if ( $actual != $expected) {
            $this->fail("Found {$actual} elements but was expected {$expected}");
        }
    }

    private function printTuple()
    {
        $sql = 'SELECT * FROM transactions';
        $actualState = $this->app['db']->fetchAll($sql);
        print_r($actualState);
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

        $underscore = new Table('underscore');
        $underscore->addColumn(
            'id',
            'integer'
        );
        $underscore->addColumn(
            'created',
            'text'
        );

        $schema = $this->app['db']->getSchemaManager();
        $schema->createTable($transactions);
        $schema->createTable($underscore);
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
