<?php
namespace Themis\Http;

use Symfony\Component\HttpFoundation\Response;

class ResponseFactory
{
    public static function unprocessable()
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        return $response;
    }

    public static function created($location)
    {
        $response = new Response();
        $response->headers->set('Location', $location);
        return $response->setStatusCode(Response::HTTP_CREATED);
    }

    public static function ok($location)
    {
        $response = new Response();
        $response->headers->set('Location', $location);
        return $response->setStatusCode(Response::HTTP_OK);
    }
}
