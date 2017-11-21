<?php

namespace CompropagoSdk\Tools;

class Request
{
    /**
     * Validates response from Request
     * 
     * @param string $response
     * @return boolean
     * @throws \Exception
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
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

    /**
     * Execute Get request
     * 
     * @param string $url
     * @param array $auth
     * @param array $headers
     * @return string
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
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

    /**
     * Execute Post request
     * 
     * @param string $url
     * @param array $data
     * @param array $auth
     * @param array $headers
     * @return string
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
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

    /**
     * Execute Put request
     * 
     * @param string $url
     * @param array $data
     * @param array $auth
     * @param array $headers
     * @return string
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
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

    /**
     * Execute Delete request
     * 
     * @param string $url
     * @param array $data
     * @param array $auth
     * @param array $headers
     * @return string
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
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