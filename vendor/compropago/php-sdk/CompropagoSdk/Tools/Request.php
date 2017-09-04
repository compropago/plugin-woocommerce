<?php

namespace CompropagoSdk\Tools;

/**
 * Class Request
 * @package CompropagoSdk\Tools
 *
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */
class Request
{
    /**
     * Validate if the source data is an error
     *
     * @param string $response
     * @return bool
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
     * Execute GET request
     *
     * @param string $url
     * @param array $auth
     * @param array $headers
     * @return mixed
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
     * Execute POST request
     *
     * @param string $url
     * @param array $data
     * @param array $auth
     * @param array $headers
     * @return mixed
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
     * Execute PUT request
     *
     * @param string $url
     * @param array $data
     * @param array $auth
     * @param array $headers
     * @return mixed
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
     * Execute DELETE request
     *
     * @param string $url
     * @param array $data
     * @param array $auth
     * @param array $headers
     * @return mixed
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