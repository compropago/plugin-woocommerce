<?php

namespace CompropagoSdk\Factory\Models;

class OrderInfo
{
    public $order_id;
    public $order_name;
    public $order_price;
    public $image_url;
    public $exchage;

    public function __construct()
    {
        $this->exchage = new Exchange();
    }
}