<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

namespace CompropagoSdk;

use CompropagoSdk\Factory\Factory;
use CompropagoSdk\Factory\Models\PlaceOrderInfo;
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
     * Get auth info
     * @return array
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
     * @return array
     * @throws \Exception
     */
    public function listDefaultProviders()
    {
        $url = $this->client->deployUri . 'providers/true';
        $response = Request::get($url);

        return Factory::getInstanceOf('ListProviders', $response);
    }

    /**
     * Get list providers by account
     * @param float $limit
     * @param string $currency
     * @return array
     * @throws \Exception
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
     * @param $orderId
     * @return \CompropagoSdk\Factory\Models\CpOrderInfo
     * @throws \Exception
     */
    public function verifyOrder($orderId)
    {
        $response = Request::get($this->client->deployUri.'charges/'.$orderId.'/', $this->getAuth());
        return Factory::getInstanceOf('CpOrderInfo', $response);
    }

    /**
     * Create new order
     * @param PlaceOrderInfo $neworder
     * @return \CompropagoSdk\Factory\Models\NewOrderInfo
     * @throws \Exception
     */
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
            'expiration_time' => $neworder->expiration_time,
            'image_url' => $neworder->image_url,
            'app_client_name' => $neworder->app_client_name,
            'app_client_version' => $neworder->app_client_version,        
        ];

        $hash = json_encode(implode(':', $this->getAuth()));
        $hash = $hash . '--' . md5($hash);

        $headers = ['Upgrade-Pay' => $hash];

        $url = $this->client->deployUri.'charges/';

        $response = Request::post($url, $params, $this->getAuth(), $headers);
        return Factory::getInstanceOf('NewOrderInfo', $response);
    }

    /**
     * Send SMS instructions for an order
     * @param string $number
     * @param string $orderId
     * @return \CompropagoSdk\Factory\Models\SmsInfo
     * @throws \Exception
     */
    public function sendSmsInstructions($number, $orderId)
    {
        $params = ['customer_phone' => $number];

        $response = Request::post($this->client->deployUri.'charges/'.$orderId.'/sms/', $params, $this->getAuth());
        return Factory::getInstanceOf('SmsInfo', $response);
    }

    /**
     * Create new webhook Url
     * @param string $url
     * @return \CompropagoSdk\Factory\Models\Webhook
     * @throws \Exception
     */
    public function createWebhook($url)
    {
        $params = [
            'url' => $url,
            'webhookType' => 'secondary'
        ];

        $response = Request::post($this->client->deployUri.'webhooks/stores/', $params, $this->getAuth());
        return Factory::getInstanceOf('Webhook', $response);
    }

    /**
     * Get list of webhooks
     * @return array
     * @throws \Exception
     */
    public function listWebhooks()
    {
        $response = Request::get($this->client->deployUri.'webhooks/stores/', $this->getAuth());
        return Factory::getInstanceOf('ListWebhooks', $response);
    }

    /**
     * Update a webhook url
     * @param string $webhookId
     * @param string $url
     * @param string $type       (secondary | primary)
     * @return \CompropagoSdk\Factory\Models\Webhook
     * @throws \Exception
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
     * @param string $webhookId
     * @return \CompropagoSdk\Factory\Models\Webhook
     * @throws \Exception
     */
    public function deactiveWebhook($webhookId)
    {
        $url = $this->client->deployUri.'webhooks/stores/'.$webhookId.'/deactive';

        $response = Request::delete($url, null, $this->getAuth());
        return Factory::getInstanceOf('Webhook', $response);
    }

    /**
     * Delete a webhook URL
     * @param string $webhookId
     * @return \CompropagoSdk\Factory\Models\Webhook
     * @throws \Exception
     */
    public function deleteWebhook($webhookId)
    {
        $response = Request::delete($this->client->deployUri.'webhooks/stores/'.$webhookId.'/', null, $this->getAuth());
        return Factory::getInstanceOf('Webhook', $response);
    }
}