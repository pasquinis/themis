<?php

namespace Themis;

use Silex\WebTestCase;

class ApplicationTest extends WebTestCase
{
    public function createApplication()
    {
        $app = new Application();
        $app = $this->debug($app);
        return $app;
    }

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
        $this->assertEquals('Hello simone', $client->getResponse()->getContent());
    }

    public function testShouldCreateATransactions()
    {
        $client = $this->createClient();
        $postParameters = [
            'operationDate' => '09/02/2017',
            'valueDate' => '09/02/2017',
            'description' => 'PAGAMENTO TRAMITE POS',
            'reason' => 'POS CARTA 124567 DEL 09/02/2017 ORE 13:44 C/O 1234567890 PINCO PALLO',
            'revenue' => 'NULL',
            'expenditure' => '-18.11',
            'currency' => 'EUR',
        ];
        $client->request('POST', '/transactions/', $postParameters);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost', $client->getResponse()->headers->get('Location'));
    }

    public function testShouldReadASelectedTransaction()
    {
        $client = $this->createClient();
        $client->request('GET', '/transactions/1');

        $expectedResponse = json_encode(
            [
                'id' => '1',
                'operationdate' => '17/09/2011',
                'valuedate' => '17/09/2011',
                'description' => 'PAGAMENTO TRAMITE POS',
                'reason' => 'POS CARTA 124567 DEL 17/09/2011 ORE 13:44 C/O 1234567890 PINCO PALLO',
                'revenue' => 'NULL',
                'expenditure' => '-18.11',
                'currency' => 'EUR',
            ]
        );
        $this->assertIsJsonResponse($client->getResponse());
        $this->assertEquals($expectedResponse, $client->getResponse()->getContent());
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
