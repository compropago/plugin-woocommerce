<?php

namespace CompropagoSdk;

class Client
{
    const VERSION="3.0.6.1";

    const API_LIVE_URI='http://api.compropago.com/v1/';
    const API_SANDBOX_URI='http://api.compropago.com/v1/';

    public $publickey;
    public $privatekey;
    public $live;

    public $deployUri;

    public $api;

    public function __construct($publickey, $privatekey, $live)
    {
        $this->publickey = $publickey;
        $this->privatekey = $privatekey;
        $this->live = $live;

        $this->deployUri = ($live === true) ? self::API_LIVE_URI : self::API_SANDBOX_URI;

        $this->api = new Service($this);
    }

    public function getUser()
    {
        return $this->privatekey;
    }

    public function getPass()
    {
        return $this->publickey;
    }
}