<?php

namespace CompropagoSdk\Factory\Models;

use CompropagoSdk\Client;
use CompropagoSdk\Service;



class PlaceOrderInfo
{
    public $order_id;
    public $order_name;
    public $order_price;
    public $customer_name;
    public $customer_email;
    public $payment_type;
    public $currency;
    public $expiration_time;
    public $image_url;
    public $app_client_name;
    public $app_client_version;
    public $latitude;
    public $longitude;
    public $cp;
    public $cutomer_phone;

    public function __construct(
        $order_id, 
        $order_name, 
        $order_price, 
        $customer_name, 
        $customer_email, 
        $payment_type="OXXO",
        $currency="MXN",
        $expiration_time=null,
        $image_url=null, 
        $app_client_name="phpsdk", 
        $app_client_version=Client::VERSION,
        $latitude = null,
        $longitude = null,
        $cp = null
        )
    {
        $this->order_id           = $order_id;
        $this->order_name         = $order_name;
        $this->order_price        = $order_price;
        $this->customer_name      = $customer_name;
        $this->customer_email     = $customer_email;
        $this->payment_type       = $payment_type;
        $this->currency           = $currency;
        $this->expiration_time    = $expiration_time;
        $this->image_url          = $image_url;
        $this->app_client_name    = $app_client_name;
        $this->app_client_version = $app_client_version;
        $this->latitude           = $latitude;
        $this->longitude          = $longitude;
        $this->cp                 = $cp;

    }
}