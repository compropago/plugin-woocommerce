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


namespace CompropagoSdk\Factory\V10;


use CompropagoSdk\Factory\Abs\CpOrderInfo;
use CompropagoSdk\Models\Customer;

class CpOrderInfo10 extends CpOrderInfo
{
    public $type;
    public $object;
    public $data;

    public function __construct()
    {
        $this->data = new Data();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->data->object->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->data->object->created_at;
    }

    /**
     * @return bool
     */
    public function getPaid()
    {
        return $this->data->object->paid;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->data->object->amount;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->data->object->currency;
    }

    /**
     * @return bool
     */
    public function getRefunded()
    {
        return $this->data->object->refunded;
    }

    /**
     * @return string
     */
    public function getFee()
    {
        return $this->data->object->fee;
    }

    /**
     * @return \CompropagoSdk\Factory\Abs\FeeDetails
     */
    public function getFeeDetails()
    {
        return $this->data->object->fee_details;
    }

    /**
     * @return \CompropagoSdk\Factory\Abs\OrderInfo
     */
    public function getOrderInfo()
    {
        $order = new OrderInfo10();

        $order->order_id       = $this->data->object->payment_details->product_id;
        $order->order_price    = $this->data->object->payment_details->product_price;
        $order->order_name     = $this->data->object->payment_details->product_name;
        $order->order_id       = $this->data->object->payment_details->product_id;
        $order->payment_method = $this->data->object->payment_details->object;
        $order->store          = $this->data->object->payment_details->store;
        $order->country        = $this->data->object->payment_details->country;
        $order->image_url      = $this->data->object->payment_details->image_url;
        $order->success_url    = $this->data->object->payment_details->success_url;

        return $order;
    }

    /**
     * @return \CompropagoSdk\Models\Customer
     */
    public function getCustomer()
    {
        $customer = new Customer();

        $customer->customer_name  = $this->data->object->payment_details->customer_name;
        $customer->customer_email = $this->data->object->payment_details->customer_email;
        $customer->customer_phone = $this->data->object->payment_details->customer_phone;

        return $customer;
    }

    /**
     * @return bool
     */
    public function getCaptured()
    {
        return $this->data->object->captured;
    }

    /**
     * @return string
     */
    public function getFailureMessage()
    {
        return $this->data->object->failure_message;
    }

    /**
     * @return string
     */
    public function getFailureCode()
    {
        return $this->data->object->failure_code;
    }

    /**
     * @return double
     */
    public function getAmountRefunded()
    {
        return $this->data->object->amount_refunded;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->data->object->description;
    }

    /**
     * @return string
     */
    public function getDispute()
    {
        return $this->data->object->dispute;
    }
}