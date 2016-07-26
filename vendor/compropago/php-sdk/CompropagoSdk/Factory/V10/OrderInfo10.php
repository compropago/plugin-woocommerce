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


use CompropagoSdk\Factory\Abs\OrderInfo;

class OrderInfo10 extends OrderInfo
{
    public $order_id;
    public $order_price;
    public $order_name;
    public $payment_method;
    public $store;
    public $country;
    public $image_url;
    public $success_url;

    public function __construct()
    {
    }

    public function getOrderId()
    {
        return $this->order_id;
    }

    public function getOrderPrice()
    {
        return $this->order_price;
    }

    public function getOrderName()
    {
        return $this->order_name;
    }

    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    public function getStore()
    {
        return $this->store;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getImageUrl()
    {
        return $this->image_url;
    }

    public function getSuccessUrl()
    {
        return $this->success_url;
    }
}