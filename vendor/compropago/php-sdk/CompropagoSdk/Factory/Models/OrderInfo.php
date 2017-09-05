<?php

namespace CompropagoSdk\Factory\Models;

/**
 * Class OrderInfo
 * @package CompropagoSdk\Factory\Models
 *
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */
class OrderInfo
{
    public $order_id;
    public $order_name;
    public $order_price;
    public $payment_method;
    public $store;
    public $country;
    public $image_url;
    public $success_url;
    public $failed_url;
    public $exchage;

    public function __construct()
    {
        $this->exchage = new Exchange();
    }
}