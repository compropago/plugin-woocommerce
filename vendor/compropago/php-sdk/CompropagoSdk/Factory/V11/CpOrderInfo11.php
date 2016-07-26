<?php
/**
 * Copyright 2015 Compropago.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Compropago php-sdk
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 */


namespace CompropagoSdk\Factory\V11;


use CompropagoSdk\Factory\Abs\CpOrderInfo;
use CompropagoSdk\Models\Customer;

class CpOrderInfo11 extends CpOrderInfo
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

    public function __construct()
    {
        $this->fee_details = new FeeDetails11();
        $this->order_info = new OrderInfo11();
        $this->customer = new Customer();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getPaid()
    {
        return $this->paid;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getRefunded()
    {
        return $this->refunded;
    }

    public function getFee()
    {
        return $this->fee;
    }

    public function getFeeDetails()
    {
        return $this->fee_details;
    }

    public function getOrderInfo()
    {
        return $this->order_info;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getCaptured()
    {
        return $this->captured;
    }

    public function getFailureMessage()
    {
        return $this->failure_message;
    }

    public function getFailureCode()
    {
        return $this->failure_code;
    }

    public function getAmountRefunded()
    {
        return $this->amount_refunded;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDispute()
    {
        return $this->dispute;
    }
}