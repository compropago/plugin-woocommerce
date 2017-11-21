<?php

namespace CompropagoSdk\Factory\Models;

class NewOrderInfo
{
    public $id;
    public $short_id;
    public $object;
    public $created;
    public $exp_date;
    public $status;
    public $live_mode;
    public $order_info;
    public $fee_details;
    public $instructions;
    public $api_version;

    public function __construct()
    {
        $this->order_info = new OrderInfo();
        $this->fee_details = new FeeDetails();
        $this->instructions = new Instructions();
    }
}