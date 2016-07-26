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


/**
 * Class Http Crea llamas Http para el consumo de servicios
 * @package CompropagoSdk\Tools
 */
class Http
{
    /**
     * Inicializa el objeto Http para las peticiones
     *
     * @param null $url
     * @return resource
     */
    public static function initHttp($url = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return $ch;
    }

    /**
     * Define el metodo que ejecutara en la peticion
     *
     * @param $ch               resource    Instancia del Objeto Http
     * @param $method           string      Tipo de peticion a ejecutar
     * @throws \Exception
     */
    public static function setMethod(&$ch,$method)
    {
        switch($method){
            case 'GET':
            case 'POST':
            case 'PUT':
            case 'DELETE':
                curl_setopt($ch,CURLOPT_CUSTOMREQUEST, $method);
                break;
            default:
                throw new \Exception("Metodo no soportado");
                break;
        }
    }

    /**
     * Estable las claves de autentificacion a usar
     *
     * @param $ch       resource    Instancia del Objeto Http
     * @param $auth     string      Cadena de autentificacion del cliente
     */
    public static function setAuth(&$ch, $auth)
    {
        curl_setopt($ch, CURLOPT_USERPWD, $auth);
    }

    /**
     * Carga los campos que se enviaran dentro de la peticion
     * El formato de envio es el sieguinte:
     * campo1=valor1&campo2=valor2&.....campox=valorx
     *
     * @param $ch               resource    Instancia del Objeto Http
     * @param string $fields                Campos a incluir en la peticion
     */
    public static function setPostFields(&$ch, $fields="")
    {
        if(!empty($fields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }
    }

    /**
     * Carga los headers que se enviaran en la peticion http
     *
     * @param $ch
     * @param array $headers
     */
    public static function setHeaders(&$ch, array $headers)
    {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Ejecuta la peticion Http que se le especifique
     *
     * @param $ch
     * @return mixed
     * @throws \Exception
     */
    public static function execHttp(&$ch)
    {
        $response = curl_exec($ch);

        if(empty($response)){
            $code = curl_errno($ch);

            if ($code == 60 || $code == 77) {
                curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cacerts.pem');
                $response = curl_exec($ch);
            }

            if(empty($response)){
                $error = curl_error($ch);
                $code = curl_errno($ch);
                throw new \Exception($error, $code);
            }
        }

        return $response;
    }
}