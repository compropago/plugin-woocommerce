<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

namespace CompropagoSdk;

use CompropagoSdk\Factory\Factory;
use CompropagoSdk\Factory\Models\PlaceOrderInfo;
use CompropagoSdk\Tools\Validations;
use CompropagoSdk\Tools\Request;

class Service
{
    private $client;

    /**
     * Service constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Obtain auth array
     * @return array
     */
    private function getAuth()
    {
        return [
            'user' => $this->client->getUser(),
            'pass' => $this->client->getPass()
        ];
    }

    /**
     * List default providers without api_keys
     * @return array
     * @throws \Exception
     */
    public function listDefaultProviders()
    {
        $url = $this->client->deployUri . 'providers/true';

        $response = Request::get($url);
        Validations::validateResponse($response);

        return Factory::getInstanceOf('ListProviders', $response->body);
    }

    /**
     * Return a list of providers according thir transacction limits.
     * Need a valid Client session.
     * @param int $limit
     * @param string $currency
     * @return array
     * @throws \Exception
     */
    public function listProviders($limit = 0, $currency = 'MXN')
    {
        $url = $this->client->deployUri . 'providers/';

        if ($limit > 0) {
            $url .= '?order_total='.$limit;
        }

        if ($limit > 0 && !empty($currency) && $currency != 'MXN') {
            $url .= '&currency='.$currency;
        }

        $response = Request::get($url, array(), $this->getAuth());
        Validations::validateResponse($response);

        return Factory::getInstanceOf('ListProviders', $response->body);
    }

    /**
     * Obtain current order info
     * @param string $orderId
     * @return \CompropagoSdk\Factory\Models\CpOrderInfo
     * @throws \Exception
     */
    public function verifyOrder($orderId)
    {
        $url = $this->client->deployUri . 'charges/' . $orderId . '/';

        $response = Request::get($url, array(), $this->getAuth());
        Validations::validateResponse($response);

        return Factory::getInstanceOf('CpOrderInfo', $response->body);
    }

    /**
     * Create an instance of Info to create an order
     * @param PlaceOrderInfo $neworder
     * @return \CompropagoSdk\Factory\Models\NewOrderInfo
     * @throws \Exception
     */
    public function placeOrder(PlaceOrderInfo $neworder)
    {
        $url = $this->client->deployUri . 'charges/';

        $data = [
            'order_id' => $neworder->order_id,
            'order_name' => $neworder->order_name,
            'order_price' => $neworder->order_price,
            'customer_name' => $neworder->customer_name,
            'customer_email' => $neworder->customer_email,
            'customer_phone' => $neworder->customer_phone,
            'payment_type' => $neworder->payment_type,
            'currency' => $neworder->currency,
            'expiration_time' => $neworder->expiration_time,
            'image_url' => $neworder->image_url,
            'app_client_name' => $neworder->app_client_name,
            'app_client_version' => $neworder->app_client_version,
        ];

        if (!empty($neworder->extra)) {
            $data['extras'] = $neworder->extra;
        }

        $response = Request::post($url, $data, array(), $this->getAuth());
        Validations::validateResponse($response);

        return Factory::getInstanceOf('NewOrderInfo', $response->body);
    }

    /**
     * Send SMS instructions for an order
     * @param string $number
     * @param string $orderId
     * @return \CompropagoSdk\Factory\Models\SmsInfo
     * @throws \Exception
     */
    public function sendSmsInstructions($number,$orderId)
    {
        $url = $this->client->deployUri . 'charges/' . $orderId . '/sms/';

        $data = ['customer_phone' => $number];

        $response = Request::post($url, $data, array(), $this->getAuth());
        Validations::validateResponse($response);

        return Factory::getInstanceOf('SmsInfo', $response->body);
    }

    /**
     * Register a webhook
     * @param string $webhookUrl
     * @return \CompropagoSdk\Factory\Models\Webhook
     * @throws \Exception
     */
    public function createWebhook($webhookUrl)
    {
        $url = $this->client->deployUri . 'webhooks/stores/';
        $data = ['url' => $webhookUrl];

        $response = Request::post($url, $data, array(), $this->getAuth());
        Validations::validateResponse($response);

        return Factory::getInstanceOf('Webhook', $response->body);
    }

    /**
     * List al current webhooks
     * @return array
     * @throws \Exception
     */
    public function listWebhooks()
    {
        $url = $this->client->deployUri . 'webhooks/stores/';

        $response = Request::get($url, array(), $this->getAuth());
        Validations::validateResponse($response);

        return Factory::getInstanceOf('ListWebhooks', $response->body);
    }

    /**
     * Update the URL of a webhook
     * @param string $webhookId
     * @param string $webhookUrl
     * @return \CompropagoSdk\Factory\Models\Webhook
     * @throws \Exception
     */
    public function updateWebhook($webhookId, $webhookUrl)
    {
        $url = $this->client->deployUri . 'webhooks/stores/' . $webhookId . '/';

        $data = ['url' => $webhookUrl];

        $response = Request::put($url, $data, array(), $this->getAuth());
        Validations::validateResponse($response);

        return Factory::getInstanceOf('Webhook', $response->body);
    }

    /**
     * Delete a webhook
     * @param string $webhookId
     * @return \CompropagoSdk\Factory\Models\Webhook
     * @throws \Exception
     */
    public function deleteWebhook($webhookId)
    {
        $url = $this->client->deployUri . 'webhooks/stores/' . $webhookId . '/';

        $response = Request::delete($url, array(), array(), $this->getAuth());
        Validations::validateResponse($response);

        return Factory::getInstanceOf('Webhook', $response->body);
    }
}