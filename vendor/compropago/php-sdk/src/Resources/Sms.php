<?php

namespace CompropagoSdk\Resources;

use CompropagoSdk\Resources\AbstractResource;
use Requests;

class Sms extends AbstractResource
{
    /**
     * Sms Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->apiUrl = 'https://api.compropago.com/v1';
    }

    /**
     * Send SMS instructions for a specific order
     *
     * @param string $orderId ComproPago Order ID (ch_xxxxx-xxx-xx-xxxx-xxxx)
     * @param string $phone   Phone number to send the message
     *
     * @return array Structure with SMS information
     *
     * @throws \Exception Request error or exception
     */
    public function sendToOrder($orderId, $phone)
    {
        $endpoint = "{$this->apiUrl}/charges/{$orderId}/sms";
        $data = [
            "customer_phone" => $phone
        ];

        $res = Requests::post(
            $endpoint,
            $this->headers,
            json_encode($data),
            $this->options
        );
        $this->validateResponse($res);

        return json_decode($res->body, true);
    }
}
