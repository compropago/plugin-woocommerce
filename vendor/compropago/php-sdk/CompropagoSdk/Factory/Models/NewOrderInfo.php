<?php
/**
 * Created by PhpStorm.
 * User: Arthur
 * Date: 27/12/16
 * Time: 11:38
 */

namespace CompropagoSdk\Factory\Models;


class NewOrderInfo
{
    public $id;
    public $short_id;
    public $object;
    public $status;
    public $created;
    public $exp_date;
    public $live_mode;
    public $order_info;
    public $fee_details;
    public $instructions;

    public function __construct()
    {
        $this->order_info = new OrderInfo();
        $this->fee_details = new FeeDetails();
        $this->instructions = new Instructions();
    }
}