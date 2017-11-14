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
        $params = ['location' => $location];
        $response = ResponseFactory::created($params);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($location, $response->headers->get('location'));
    }

    public function testShouldCreateAResponseWith200Ok()
    {
        $location = 'http://location.themis.com';
        $params = ['location' => $location];
        $response = ResponseFactory::ok($params);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($location, $response->headers->get('location'));
    }

    public function testShouldCreateAResponseWith200AndContentTypeJson()
    {
        $location = 'http://location.themis.com';
        $content = ['key' => 'value'];
        $params = [
            'location' => $location,
            'content' => $content,
        ];
        $response = ResponseFactory::okInJsonOutput($params);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($location, $response->headers->get('location'));
        $this->assertEquals(json_encode($content), $response->getContent());
    }

    public function testShouldCreateAResponseWith400BadRequest()
    {
        $response = ResponseFactory::badRequest();
        $this->assertEquals(400, $response->getStatusCode());
    }
}
