<?php

namespace CompropagoSdk;

use CompropagoSdk\Factory\Factory;
use CompropagoSdk\Factory\Models\PlaceOrderInfo;
use CompropagoSdk\Tools\Request;

class Service
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function listProviders($auth = false, $limit = 0, $currency='MXN')
    {
        if ($auth) {
            $url = $this->client->deployUri.'providers/';
            $keys = ['user' => $this->client->getUser(), 'pass' => $this->client->getPass()];
        } else {
            $url = $this->client->deployUri.'providers/true/';
            $keys = [];
        }

        if ($limit > 0) {
            $url .= '?order_total='.$limit;
        }

        if ($limit > 0 && !empty($currency) && $currency != 'MXN') {
            $url .= '&currency='.$currency;
        }

        $response = Request::get($url, $keys);

        return Factory::getInstanceOf('ListProviders', $response);
    }

    public function verifyOrder( $orderId )
    {
        $response = Request::get(
            $this->client->deployUri.'charges/'.$orderId.'/',
            ['user' => $this->client->getUser(), 'pass' => $this->client->getPass()]
        );

        return Factory::getInstanceOf('CpOrderInfo', $response);
    }

    public function placeOrder(PlaceOrderInfo $neworder)
    {
        $params = [
            'order_id' => $neworder->order_id,
            'order_name' => $neworder->order_name,
            'order_price' => $neworder->order_price,
            'customer_name' => $neworder->customer_name,
            'customer_email' => $neworder->customer_email,
            'payment_type' => $neworder->payment_type,
            'currency' => $neworder->currency,
            'image_url' => $neworder->image_url,
            'app_client_name' => $neworder->app_client_name,
            'app_client_version' => $neworder->app_client_version
        ];

        $response = Request::post(
            $this->client->deployUri.'charges/',
            $params,
            ['user' => $this->client->getUser(), 'pass' => $this->client->getPass()]
        );

        return Factory::getInstanceOf('NewOrderInfo', $response);
    }

    public function sendSmsInstructions($number,$orderId)
    {
        $params = ['customer_phone' => $number];

        $response = Request::post(
            $this->client->deployUri.'charges/'.$orderId.'/sms/',
            $params,
            ['user' => $this->client->getUser(), 'pass' => $this->client->getPass()]
        );

        return Factory::getInstanceOf('SmsInfo', $response);
    }

    public function createWebhook($url)
    {
        $params = ['url' => $url];

        $response = Request::post(
            $this->client->deployUri.'webhooks/stores/',
            $params,
            ['user' => $this->client->getUser(), 'pass' => $this->client->getPass()]
        );

        return Factory::getInstanceOf('Webhook', $response);
    }

    public function listWebhooks()
    {
        $response = Request::get(
            $this->client->deployUri.'webhooks/stores/',
            ['user' => $this->client->getUser(), 'pass' => $this->client->getPass()]
        );

        return Factory::getInstanceOf('ListWebhooks', $response);
    }

    public function updateWebhook($webhookId, $url)
    {
        $params = ['url' => $url];

        $response = Request::put(
            $this->client->deployUri.'webhooks/stores/'.$webhookId.'/',
            $params,
            ['user' => $this->client->getUser(), 'pass' => $this->client->getPass()]
        );

        return Factory::getInstanceOf('Webhook', $response);
    }

    public function deleteWebhook($webhookId)
    {
        $response = Request::delete(
            $this->client->deployUri.'webhooks/stores/'.$webhookId.'/',
            null,
            ['user' => $this->client->getUser(), 'pass' => $this->client->getPass()]
        );

        return Factory::getInstanceOf('Webhook', $response);
    }
}