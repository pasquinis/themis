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
}
