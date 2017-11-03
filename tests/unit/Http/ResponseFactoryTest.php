<?php
namespace Themis\Http;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldCreateAResponseWithValue422()
    {
        $response = ResponseFactory::unprocessable();
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testShouldCreateAResponseWith201Created()
    {
        $location = 'http://location.themis.com';
        $response = ResponseFactory::created($location);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($location, $response->headers->get('location'));
    }

    public function testShouldCreateAResponseWith200Ok()
    {
        $location = 'http://location.themis.com';
        $response = ResponseFactory::ok($location);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($location, $response->headers->get('location'));
    }
}
