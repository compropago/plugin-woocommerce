<?php

namespace CompropagoSdk\Resources\Payments;

use CompropagoSdk\Resources\AbstractResource;
use Requests;

class Spei extends AbstractResource
{
    /**
     * Spei Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->apiUrl = 'https://api.compropago.com/v2';
    }

    /**
     * Create a SPEI order in ComproPago
     *
     * @param array $data Order information
     *
     * @return array Structure with spei order information
     */
    public function createOrder($data)
    {
        $endpoint = "{$this->apiUrl}/orders";

        $res = Requests::post(
            $endpoint,
            $this->headers,
            json_encode($data),
            $this->options
        );
        $this->validateResponse($res);

        return json_decode($res->body, true);
    }

    /**
     * Verify the information of a spei order
     *
     * @param string $orderId Spei order ID
     *
     * @return array Structure with spei order nformation
     *
     * @throws \Exception Request error or exception
     */
    public function verifyOrder($orderId)
    {
        $endpoint = "{$this->apiUrl}/orders/{$orderId}";

        $res = Requests::get(
            $endpoint,
            array(),
            $this->options
        );
        $this->validateResponse($res);

        return json_decode($res->body, true);
    }
}
