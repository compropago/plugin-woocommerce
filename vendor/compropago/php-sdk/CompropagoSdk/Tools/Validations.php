<?php
/**
 * Copyright 2015 Compropago.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Compropago php-sdk
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 */


namespace CompropagoSdk\Tools;


use CompropagoSdk\Client;
use CompropagoSdk\Factory\Factory;


/**
 * Class Validations Contiene las validaciones generales para el uso de los servicios
 * @package CompropagoSdk\Tools
 */
class Validations
{

    /**
     * Evalua que el cliente pueda autentificarse correctamente
     *
     * @param Client $client
     * @return \CompropagoSdk\Models\EvalAuthInfo
     * @throws \Exception
     */
    public static function evalAuth( Client $client )
    {
        $response = Rest::get($client->getUri()."users/auth/", $client->getFullAuth());
        $info = Factory::evalAuthInfo($response);

        switch($info->code){
            case '200':
                return $info;
            default:
                throw new \Exception("CODE {$info->code}: ".$info->message,$info->code);
        }
    }

    /**
     * Valida que el cliente pueda realizar transacciones
     *
     * @param Client $client
     * @return bool
     * @throws \Exception
     */
    public static function validateGateway( Client $client )
    {
        if(empty($client)){
            throw new \Exception("El objecto Client no es valido");
        }

        $clientMode = $client->getMode();

        $authinfo = self::evalAuth($client);

        if($authinfo->mode_key != $authinfo->livemode){
            throw new \Exception("Las llaves no corresponden a modo de la tienda");
        }

        if($clientMode != $authinfo->livemode){
            throw new \Exception("El modo del cliente no corresponde al de la tienda");
        }

        if($clientMode != $authinfo->mode_key){
            throw new \Exception("El modo del cliente no corresponde al de las llaves");
        }

        return true;
    }

}