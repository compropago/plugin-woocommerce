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


namespace CompropagoSdk;

/**
 * Class Client Clase principal que procee acceso a los servicios de API
 * @package CompropagoSdk
 */
class Client
{
    const VERSION="2.0.0-alfa";

    #const API_LIVE_URI='http://api-staging-compropago.herokuapp.com/v1/';
    #const API_SANDBOX_URI='http://api-staging-compropago.herokuapp.com/v1/';

    #const API_LIVE_URI='https://node-test-cp.herokuapp.com/v1/';
    //const API_SANDBOX_URI='https://node-test-cp.herokuapp.com/v1/';

    const API_LIVE_URI='http://api.compropago.com/v1/';
    const API_SANDBOX_URI='http://api.compropago.com/v1/';

    const USER_AGENT_SUFFIX = "compropago-php-sdk";

    public $api;

    private $publickey;
    private $privatekey;
    private $live;
    private $contained;

    private $deployUri;

    public function __construct($publickey, $privatekey, $live, $contained = null)
    {
        $this->publickey = $publickey;
        $this->privatekey = $privatekey;
        $this->live = $live;

        $this->contained = !empty($contained) ? $contained : "SDK; phpsdk ".self::VERSION.";";

        $this->deployUri = ($live === true) ? self::API_LIVE_URI : self::API_SANDBOX_URI;

        $this->api = new Service($this);
    }

    /**
     * @return string
     */
    public function getAuth()
    {
        return $this->privatekey.":";
    }

    /**
     * @return string
     */
    public function getFullAuth()
    {
        return $this->privatekey.":".$this->publickey;
    }

    /**
     * @return bool
     */
    public function getMode()
    {
        return $this->live;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->deployUri;
    }

    /**
     * @return null|string
     */
    public function getContained()
    {
        return $this->contained;
    }
}