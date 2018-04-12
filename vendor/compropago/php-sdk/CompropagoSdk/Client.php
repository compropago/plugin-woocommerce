<?php

namespace CompropagoSdk;


class Client
{
    const VERSION = "4.0.1.0";
    const API_LIVE_URI = 'https://api.compropago.com/v1/';
    const API_SANDBOX_URI = 'https://api.compropago.com/v1/';

    public $api;
    public $live;
    public $deployUri;
    public $publickey;
    public $privatekey;

    /**
     * Client constructor.
     * @param $publickey
     * @param $privatekey
     * @param $live
     */
    public function __construct($publickey, $privatekey, $live)
    {
        $this->publickey = $publickey;
        $this->privatekey = $privatekey;
        $this->live = $live;

        $this->deployUri = ($live === true) ? self::API_LIVE_URI : self::API_SANDBOX_URI;

        $this->api = new Service($this);
    }

    /**
     * Return the user of the API
     * @return string mixed
     */
    public function getUser()
    {
        return $this->privatekey;
    }

    /**
     * Return the password for the API
     * @return string mixed
     */
    public function getPass()
    {
        return $this->publickey;
    }

    /**
     * Autoload SDK classes
     */
    public static function register_autoload()
    {
        spl_autoload_register(function ($class) {
            if (strpos($class, 'CompropagoSdk') !== 0) {
                return;
            }

            $class = str_replace('\\', '/', $class);

            $path = __DIR__ . '/../' . $class . '.php';

            if (file_exists($path)) {
                require_once $path;
            }
        });
    }
}