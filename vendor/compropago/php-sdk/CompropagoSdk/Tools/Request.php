<?php

namespace CompropagoSdk\Tools;

class Request
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    /**
     * Execute Get request
     * @param string $url
     * @param array $auth
     * @param array $headers
     * @return HttpResponse
     * @throws \Exception
     */
    public static function get($url, $headers=array(), $auth=array())
    {
        $http = new Http($url);
        $http->setAuth($auth);
        $http->setMethod(self::GET);
        $http->setHeaders($headers);
        $res = $http->executeRequest();

        return $res;
    }

    /**
     * Execute Post request
     * @param string $url
     * @param array $data
     * @param array $auth
     * @param array $headers
     * @return HttpResponse
     * @throws \Exception
     */
    public static function post($url, $data=array(), $headers=array(), $auth=array())
    {
        $http = new Http($url);
        $http->setAuth($auth);
        $http->setJsonData($data);
        $http->setMethod(self::POST);
        $http->setHeaders($headers);
        $res = $http->executeRequest();

        return $res;
    }

    /**
     * Execute Put request
     * @param string $url
     * @param array $data
     * @param array $auth
     * @param array $headers
     * @return HttpResponse
     * @throws \Exception
     */
    public static function put($url, $data=array(), $headers=array(), $auth=array())
    {
        $http = new Http($url);
        $http->setAuth($auth);
        $http->setJsonData($data);
        $http->setMethod(self::PUT);
        $http->setHeaders($headers);
        $res = $http->executeRequest();

        return $res;
    }

    /**
     * Execute Delete request
     * @param string $url
     * @param array $data
     * @param array $auth
     * @param array $headers
     * @return HttpResponse
     * @throws \Exception
     */
    public static function delete($url, $data=array(), $headers=array(), $auth=array())
    {
        $http = new Http($url);
        $http->setAuth($auth);
        $http->setJsonData($data);
        $http->setMethod(self::DELETE);
        $http->setHeaders($headers);
        $res = $http->executeRequest();

        return $res;
    }
}