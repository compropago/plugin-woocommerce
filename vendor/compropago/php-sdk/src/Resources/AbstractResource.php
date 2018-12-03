<?php

namespace CompropagoSdk\Resources;

use CompropagoSdk\Helpers\ValidationHelper;

abstract class AbstractResource
{
    use ValidationHelper;

    /**
     * Authorization for the API requests
     *
     * @var array
     */
    protected $options = [];

    /**
     * Base API url for the endpoints
     *
     * @var string
     */
    protected $apiUrl = '';

    /**
     * Shared headers between resources
     *
     * @var array
     */
    protected $headers = [];

    /**
     * AbstractResource Construct
     */
    public function __construct()
    {
        $this->options = ['auth' => null];
        $this->headers = ['Content-Type' => 'application/json'];
    }

    /**
     * Set keys for the ComproPago API
     *
     * @param string $public  Public key of ComproPago panel
     * @param string $private Private key of ComproPago panel
     *
     * @return AbstractResorce Self resource instance
     */
    public function withKeys($public, $private)
    {
        $this->options['auth'] = [$private, $public];
        return $this;
    }

    /**
     * Return an array with the auth information of the request
     *
     * @return array Auth array data
     */
    public function getAuth()
    {
        return $this->options['auth'];
    }
}
