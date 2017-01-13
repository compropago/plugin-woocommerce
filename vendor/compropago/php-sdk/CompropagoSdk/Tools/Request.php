<?php

namespace CompropagoSdk\Tools;

class Request
{
    private static function validateResponse($response)
    {
        if (!empty($response)) {
            $aux = json_decode($response, true);

            if (isset($aux['type']) && $aux['type'] == 'error') {
                throw new \Exception('Error: '.$aux['message']);
            } else {
                return true;
            }
        } else {
            throw new \Exception('Empty response');
        }
    }

    public static function get($url, $auth=array(), $headers=array())
    {
        $http = new Http($url);
        $http->setAuth($auth);
        $http->setMethod('GET');
        $http->setExtraHeaders($headers);
        $res = $http->executeRequest();

        self::validateResponse($res);

        return $res;

    }

    public static function post($url, $data=array(), $auth=array(), $headers=array())
    {
        $http = new Http($url);
        $http->setAuth($auth);
        if (!empty($data)) : $http->setData($data); endif;
        $http->setMethod('POST');
        $http->setExtraHeaders($headers);
        $res = $http->executeRequest();

        self::validateResponse($res);

        return $res;
    }

    public static function put($url, $data=array(), $auth=array(), $headers=array())
    {
        $http = new Http($url);
        $http->setAuth($auth);
        if (!empty($data)) : $http->setData($data); endif;
        $http->setMethod('PUT');
        $http->setExtraHeaders($headers);
        $res = $http->executeRequest();

        self::validateResponse($res);

        return $res;
    }

    public static function delete($url, $data=array(), $auth=array(), $headers=array())
    {
        $http = new Http($url);
        $http->setAuth($auth);
        if (!empty($data)) : $http->setData($data); endif;
        $http->setMethod('DELETE');
        $http->setExtraHeaders($headers);
        $res = $http->executeRequest();

        self::validateResponse($res);

        return $res;
    }
}