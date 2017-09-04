<?php

namespace CompropagoSdk\Factory\Models;

/**
 * Class NewOrderInfo
 * @package CompropagoSdk\Factory\Models
 *
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */
class NewOrderInfo
{
    public $id;
    public $short_id;
    public $type;
    public $object;
    public $livemode;
    public $created_at;
    public $accepted_at;
    public $expires_at;
    public $paid;
    public $amount;
    public $currency;
    public $refunded;
    public $fee;
    public $fee_details;
    public $order_info;
    public $customer;
    public $instructions;
    public $api_version;

    public function __construct()
    {
        $this->order_info = new OrderInfo();
        $this->fee_details = new FeeDetails();
        $this->instructions = new Instructions();
        $this->customer = new Customer();
    }
}