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

    /**
     * @return array
     */
    private function getAuth()
    {
        return [
            "user" => $this->client->getUser(),
            "pass" => $this->client->getPass()
        ];
    }

    public function listDefaultProviders()
    {
        $url = $this->client->deployUri . 'providers/true';
        $response = Request::get($url);

        return Factory::getInstanceOf('ListProviders', $response);
    }

    /**
     * @param int $limit
     * @param string $currency
     * @return array
     */
    public function listProviders($limit = 0, $currency='MXN')
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
     * @param $orderId
     * @return \CompropagoSdk\Factory\Models\CpOrderInfo
     */
    public function verifyOrder( $orderId )
    {
        $response = Request::get($this->client->deployUri.'charges/'.$orderId.'/', $this->getAuth());
        return Factory::getInstanceOf('CpOrderInfo', $response);
    }

    /**
     * @param PlaceOrderInfo $neworder
     * @return \CompropagoSdk\Factory\Models\NewOrderInfo
     */
    public function placeOrder(PlaceOrderInfo $neworder)
    {
        $ip = self::getIp();
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
            'customer' => [
                'name'=> $neworder->customer_name,
                'email'=> $neworder->customer_email,
                'phone'=> $neworder->cutomer_phone,
                'cp'=> $neworder->cp,
                'ip_address'=> $ip,
                'glocation' => [
                    'lat'=> $neworder->latitude,
                    'lon'=> $neworder->longitude
                ]
                        
            ]
            
        ];

        $response = Request::post($this->client->deployUri.'charges/', $params, $this->getAuth());
        return Factory::getInstanceOf('NewOrderInfo', $response);
    }

    /**
     * @param $number
     * @param $orderId
     * @return \CompropagoSdk\Factory\Models\SmsInfo
     */
    public function sendSmsInstructions($number,$orderId)
    {
        $params = ['customer_phone' => $number];

        $response = Request::post($this->client->deployUri.'charges/'.$orderId.'/sms/', $params, $this->getAuth());
        return Factory::getInstanceOf('SmsInfo', $response);
    }

    /**
     * @param $url
     * @return \CompropagoSdk\Factory\Models\Webhook
     */
    public function createWebhook($url)
    {
        $params = ['url' => $url];

        $response = Request::post($this->client->deployUri.'webhooks/stores/', $params, $this->getAuth());
        return Factory::getInstanceOf('Webhook', $response);
    }

    /**
     * @return array
     */
    public function listWebhooks()
    {
        $response = Request::get($this->client->deployUri.'webhooks/stores/', $this->getAuth());
        return Factory::getInstanceOf('ListWebhooks', $response);
    }

    /**
     * @param $webhookId
     * @param $url
     * @return \CompropagoSdk\Factory\Models\Webhook
     */
    public function updateWebhook($webhookId, $url)
    {
        $params = ['url' => $url];

        $response = Request::put($this->client->deployUri.'webhooks/stores/'.$webhookId.'/', $params, $this->getAuth());
        return Factory::getInstanceOf('Webhook', $response);
    }

    /**
     * @param $webhookId
     * @return \CompropagoSdk\Factory\Models\Webhook
     */
    public function deleteWebhook($webhookId)
    {
        $response = Request::delete($this->client->deployUri.'webhooks/stores/'.$webhookId.'/', null, $this->getAuth());
        return Factory::getInstanceOf('Webhook', $response);
    }

    private function getIp(){
        if(isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if(isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else{
            $ip = '';
        }
        
        return $ip;
    }
}