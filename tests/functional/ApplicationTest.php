<?php

namespace Themis;

use Silex\WebTestCase;

class ApplicationTest extends WebTestCase
{
    public function createApplication()
    {
        $app = new Application();
        // $app = $this->debug($app);
        return $app;
    }

    public function testShouldReturn404WhenICallHomeRoot()
    {
        $client = $this->createClient();
        $client->request('GET', '/');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testShouldReturnTheCorrectContentWhenICallRouteHelloWithSimoneAsParameter()
    {
        $client = $this->createClient();
        $client->request('GET', '/hello/simone/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
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
    }

    private function debug(Application $app)
    {
        $app['debug'] = true;
        unset($app['exception_handler']);
        return $app;
    }
}
