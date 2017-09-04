<?php

namespace CompropagoSdk;

use CompropagoSdk\Factory\Factory;
use CompropagoSdk\Tools\Request;

/**
 * Class Service
 * @package CompropagoSdk
 *
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */
class Service
{
    private $client;

    /**
     * Service constructor.
     *
     * @param Client $client
     *
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get auth info
     *
     * @return array
     * 
     * @author Eduardo Aguilar <dante.aguilar@gmail.com>
     */
    private function getAuth()
    {
        return [
            "user" => $this->client->getUser(),
            "pass" => $this->client->getPass()
        ];
    }

    /**
     * Get default Providers
     *
     * @return array
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public function listDefaultProviders()
    {
        $url = $this->client->deployUri . 'providers/true';
        $response = Request::get($url);

        return Factory::getInstanceOf('ListProviders', $response);
    }

    /**
     * Get list providers by account
     *
     * @param float $limit
     * @param string $currency
     * @return array
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public function listProviders($limit = 0.0, $currency='MXN')
    {
        $url = $this->client->deployUri . 'providers/';

        if ($limit > 0) {
            $url .= '?order_total='.$limit;
        }

        if ($limit > 0 && !empty($currency) && $currency != 'MXN') {
            $url .= '&currency='.$currency;
        }

        $response = Request::get($url, $this->getAuth());

        return Factory::getInstanceOf('ListProviders', $response);
    }

    /**
     * Get info of an order
     *
     * @param string $orderId
     * @return \CompropagoSdk\Factory\Models\CpOrderInfo
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public function verifyOrder($orderId)
    {
        $response = Request::get($this->client->deployUri.'charges/'.$orderId.'/', $this->getAuth());
        return Factory::getInstanceOf('CpOrderInfo', $response);
    }

    /**
     * Create new order
     *
     * @param \CompropagoSdk\Factory\Models\PlaceOrderInfo $neworder
     * @return \CompropagoSdk\Factory\Models\NewOrderInfo
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public function placeOrder($neworder)
    {
        $params = [
            'order_id' => $neworder->order_id,
            'order_name' => $neworder->order_name,
            'order_price' => $neworder->order_price,
            'customer_name' => $neworder->customer_name,
            'customer_email' => $neworder->customer_email,
            'payment_type' => $neworder->payment_type,
            'currency' => $neworder->currency,
            'expiration_time' => $neworder->expiration_time,
            'image_url' => $neworder->image_url,
            'app_client_name' => $neworder->app_client_name,
            'app_client_version' => $neworder->app_client_version,        
        ];

        $response = Request::post($this->client->deployUri.'charges/', $params, $this->getAuth());
        return Factory::getInstanceOf('NewOrderInfo', $response);
    }

    /**
     * Send SMS instructions for an order
     *
     * @param string $number
     * @param string $orderId
     * @return \CompropagoSdk\Factory\Models\SmsInfo
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public function sendSmsInstructions($number,$orderId)
    {
        $params = ['customer_phone' => $number];

        $response = Request::post($this->client->deployUri.'charges/'.$orderId.'/sms/', $params, $this->getAuth());
        return Factory::getInstanceOf('SmsInfo', $response);
    }

    /**
     * Create new webhook Url
     *
     * @param string $url
     * @return \CompropagoSdk\Factory\Models\Webhook
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public function createWebhook($url)
    {
        $params = ['url' => $url];

        $response = Request::post($this->client->deployUri.'webhooks/stores/', $params, $this->getAuth());
        return Factory::getInstanceOf('Webhook', $response);
    }

    /**
     * Get list of webhooks
     *
     * @return array
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public function listWebhooks()
    {
        $response = Request::get($this->client->deployUri.'webhooks/stores/', $this->getAuth());
        return Factory::getInstanceOf('ListWebhooks', $response);
    }

    /**
     * Update a webhook url
     *
     * @param string $webhookId
     * @param string $url
     * @param string $type (secondary | primary)
     * @return \CompropagoSdk\Factory\Models\Webhook
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public function updateWebhook($webhookId, $url=null, $type=null)
    {
        $params = [
            'url' => $url,
            'webhookType' => $type
        ];

        $response = Request::put($this->client->deployUri.'webhooks/stores/'.$webhookId.'/', $params, $this->getAuth());
        return Factory::getInstanceOf('Webhook', $response);
    }

    /**
     * Deactive a webhook URL
     *
     * @param string $webhookId
     * @return \CompropagoSdk\Factory\Models\Webhook
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public function deactiveWebhook($webhookId)
    {
        $url = $this->client->deployUri.'webhooks/stores/'.$webhookId.'/deactive';

        $response = Request::delete($url, null, $this->getAuth());
        return Factory::getInstanceOf('Webhook', $response);
    }

    /**
     * Delete a webhook URL
     *
     * @param string $webhookId
     * @return \CompropagoSdk\Factory\Models\Webhook
     * 
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public function deleteWebhook($webhookId)
    {
        $response = Request::delete($this->client->deployUri.'webhooks/stores/'.$webhookId.'/', null, $this->getAuth());
        return Factory::getInstanceOf('Webhook', $response);
    }
}