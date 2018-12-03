<?php

namespace CompropagoSdk\Resources\Payments;

use CompropagoSdk\Resources\AbstractResource;
use Requests;

class Cash extends AbstractResource
{
    /**
     * Cash Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->apiUrl = 'https://api.compropago.com/v1';
    }

    /**
     * Return a list of the default Cash Providers of ComproPago
     *
     * @return array List of all posible providers
     *
     * @throws \Exception Request error or exception
     */
    public function getDefaultProviders()
    {
        $endpoint = "{$this->apiUrl}/providers/true";

        $res = Requests::get($endpoint);
        $this->validateResponse($res);

        return json_decode($res->body, true);
    }

    /**
     * Return a specific providers filtered by CP keys and order amounr
     *
     * @param float  $limit    Minimum limit amount that the provider has to support
     * @param string $currency Currency of the limit amount
     *
     * @return array List of filtered providers
     *
     * @throws \Exception Request error or exception
     */
    public function getProviders($limit = 0, $currency = 'MXN')
    {
        $endpoint = "{$this->apiUrl}/providers";

        if ($limit > 0) {
            $endpoint .= '?order_total='.$limit;
        }

        if ($limit > 0 && !empty($currency) && $currency != 'MXN') {
            $endpoint .= '&currency='.$currency;
        }

        $res = Requests::get($endpoint, [], $this->options);
        $this->validateResponse($res);

        return json_decode($res->body, true);
    }

    /**
     * Create a cash order in ComproPago
     *
     * @param array $data Order information like customer data, price currency an product
     *
     * @return array Structure with order details
     *
     * @throws \Exception Request error or exception
     */
    public function createOrder($data)
    {
        $endpoint = "{$this->apiUrl}/charges";

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
     * Verify order status by id
     *
     * @param string $orderId Order id
     *
     * @return array Structure with order details
     *
     * @throws \Exception Request error or exception
     */
    public function verifyOrder($orderId)
    {
        $endpoint = "{$this->apiUrl}/charges/{$orderId}";

        $res = Requests::get(
            $endpoint,
            array(),
            $this->options
        );
        $this->validateResponse($res);

        return json_decode($res->body, true);
    }
}
