<?php

namespace Themis;

use Silex\WebTestCase;

class ApplicationTest extends WebTestCase
{
    public function createApplication()
    {
        $app = new Application();
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
}
