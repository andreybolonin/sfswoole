<?php

namespace Swoole\HttpServerBundle\Http;

use Swoole\Http\Response;
use Symfony\Component\HttpFoundation\Request;

class Http
{
    public static function createSfRequest($swRequest)
    {
        $_SERVER = isset($swRequest->server) ? array_change_key_case($swRequest->server, CASE_UPPER) : [];
        if (isset($swRequest->header)) {
            $headers = [];
            foreach ($swRequest->header as $k => $v) {
                $k = str_replace('-', '_', $k);
                $headers['http_'.$k] = $v;
            }
            $_SERVER += array_change_key_case($headers, CASE_UPPER);
        }

        $_GET = isset($swRequest->get) ? $swRequest->get : [];
        $_POST = isset($swRequest->post) ? $swRequest->post : [];
        $_COOKIE = isset($swRequest->cookie) ? $swRequest->cookie : [];

        $sfRequest = Request::createFromGlobals();
        if (0 === strpos($sfRequest->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($swRequest->rawContent(), true);
            $sfRequest->request->replace(is_array($data) ? $data : []);
        }

        return $sfRequest;
    }

    public static function createSwResponse(Response $swResponse, \Symfony\Component\HttpFoundation\Response $sfResponse)
    {
        foreach ($sfResponse->headers->getCookies() as $cookie) {
            $swResponse->header('Set-Cookie', $cookie);
        }

        foreach ($sfResponse->headers as $name => $values) {
            $name = implode('-', array_map('ucfirst', explode('-', $name)));
            foreach ($values as $value) {
                $swResponse->header($name, $value);
            }
        }

        return $sfResponse->getContent();
    }
}
