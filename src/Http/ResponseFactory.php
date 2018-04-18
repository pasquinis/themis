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

    public static function created($params)
    {
        $response = new Response();
        $response->headers->set('Location', $params['location']);
        return $response->setStatusCode(Response::HTTP_CREATED);
    }

    public static function ok($params = null)
    {
        $response = new Response();
        if (isset($params['location'])) {
            $response->headers->set('Location', $params['location']);
        }
        return $response->setStatusCode(Response::HTTP_OK);
    }

    public static function okInJsonOutput($params)
    {
        $response = new Response();
        if (isset($params['location'])) {
            $response->headers->set('Location', $params['location']);
        }
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($params['content']));
        return $response->setStatusCode(Response::HTTP_OK);
    }

    public static function badRequest()
    {
        $response = new Response();
        return $response->setStatusCode(Response::HTTP_BAD_REQUEST);
    }
}
