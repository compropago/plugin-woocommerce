<?php

namespace CompropagoSdk\Factory\Models;

class CpOrderInfo
{
    public $id;
    public $type;
    public $object;
    public $created;
    public $paid;
    public $amount;
    public $livemode;
    public $currency;
    public $refunded;
    public $fee;
    public $fee_details;
    public $order_info;
    public $customer;
    public $captured;
    public $failure_message;
    public $failure_code;
    public $amount_refunded;
    public $description;
    public $dispute;
    public $api_version;

    public function __construct()
    {
        $this->fee_details = new FeeDetails();
        $this->order_info = new OrderInfo();
        $this->customer = new Customer();
    }
}