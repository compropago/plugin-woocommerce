<?php

namespace CompropagoSdk;

use CompropagoSdk\Resources\AbstractResource;

class Client
{
    /**
     * Public key of compropago
     *
     * @var string
     */
    private $publicKey;

    /**
     * Pravite key of compropago
     *
     * @var string
     */
    private $privateKey;

    /**
     * Client constructor
     *
     * @param string $publicKey  Public key of the ComproPago config panel
     * @param string $privateKey Private key of the ComproPago config panel
     */
    public function __construct($publicKey = null, $privateKey = null)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * Set up ComproPago keys
     *
     * @param string $public  Public key of the ComproPago config panel
     * @param string $private Private key of the ComproPago config panel
     *
     * @return Client Self instance
     */
    public function withKeys($public = null, $private = null)
    {
        $this->publicKey = $public;
        $this->privateKey = $private;
        return $this;
    }

    /**
     * Get the configurated public key
     *
     * @return string Registed public key
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * Get the configurated private key
     *
     * @return string Registed private key
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * Generates an instance of a resource whit the configurated keys
     *
     * @param string $class Fully cualified resource name
     *
     * @return AbstractResource Instance of a specific resource
     */
    public function getResource($class)
    {
        $resource = (new $class)->withKeys(
            $this->publicKey,
            $this->privateKey
        );

        return $resource;
    }
}
