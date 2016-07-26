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


namespace CompropagoSdk\Models;

/**
 * Class PlaceOrderInfo informacion de nuevas ordenes
 * @package CompropagoSdk\Models
 */
class PlaceOrderInfo
{
    public $order_id;
    public $order_name;
    public $order_price;
    public $customer_name;
    public $customer_email;
    public $payment_type;
    public $image_url;
    public $app_client_name;
    public $app_client_version;

    public function __construct($order_id, $order_name, $order_price, $customer_name, $customer_email, $payment_type="OXXO", $image_url=null, $app_client_name="phpsdk", $app_client_version="2.0.0-alfa")
    {
        $this->order_id           = $order_id;
        $this->order_name         = $order_name;
        $this->order_price        = $order_price;
        $this->customer_name      = $customer_name;
        $this->customer_email     = $customer_email;
        $this->payment_type       = $payment_type;
        $this->image_url          = $image_url;
        $this->app_client_name    = $app_client_name;
        $this->app_client_version = $app_client_version;
    }
}