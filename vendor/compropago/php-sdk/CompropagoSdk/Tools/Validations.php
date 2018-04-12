<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

namespace CompropagoSdk\Tools;

use CompropagoSdk\Client;
use CompropagoSdk\Factory\Factory;

class Validations
{
    /**
     * Verify Client Auth
     * @param Client $client
     * @return \CompropagoSdk\Factory\Models\EvalAuthInfo
     * @throws \Exception
     */
    public static function evalAuth(Client $client)
    {
        $url = $client->deployUri . "users/auth/";
        $auth = [
            'user' => $client->getUser(),
            'pass' => $client->getPass()
        ];

        $response = Request::get($url, array(), $auth);
        self::validateResponse($response);

        return Factory::getInstanceOf('EvalAuthInfo', $response->body);
    }

    /**
     * Validates response from Request
     * @param \CompropagoSdk\Tools\HttpResponse $response
     * @return bool
     * @throws \Exception
     */
    public static function validateResponse($response)
    {
        $code = $response->statusCode;
        $body = $response->body;

        if ($code != 200) {
            $message = 'Request error: ' . $code;
            throw new \Exception($message);
        }

        if (!empty($body)) {
            $aux = json_decode($body, true);

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
     * Validate Gateway errors
     * @param Client $client
     * @return boolean
     * @throws \Exception
     */
    public static function validateGateway(Client $client)
    {
        if(empty($client)){
            throw new \Exception("Client object is not valid");
        }

        $clientMode = $client->live;

        $authinfo = self::evalAuth($client);

        if($authinfo->mode_key != $authinfo->livemode){
            throw new \Exception("Keys are diferent of store mode.");
        }

        if($clientMode != $authinfo->livemode){
            throw new \Exception("Client mode is diferent of store mode");
        }

        if($clientMode != $authinfo->mode_key){
            throw new \Exception("Client mode is diferent of keys mode");
        }

        return true;
    }
}