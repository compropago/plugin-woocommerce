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
 * Compropago ${LIBRARI}
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 */


namespace CompropagoSdk\Factory\V10;


use CompropagoSdk\Factory\Abs\FeeDetails;
use CompropagoSdk\Factory\Abs\Instructions;
use CompropagoSdk\Factory\Abs\NewOrderInfo;
use CompropagoSdk\Factory\Abs\OrderInfo;

class NewOrderInfo10 extends NewOrderInfo
{
    public $payment_id;
    public $short_payment_id;
    public $payment_status;
    public $creation_date;
    public $expiration_date;
    public $product_information;
    public $payment_instructions;

    public function __construct()
    {
        $this->product_information = new ProductInformation();
        $this->payment_instructions = new Instructions10();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->payment_id;
    }

    /**
     * @return string
     */
    public function getShortId()
    {
        return $this->short_payment_id;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->payment_status;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->creation_date;
    }

    /**
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expiration_date;
    }

    /**
     * @return OrderInfo
     */
    public function getOrderInfo()
    {
        $info = new OrderInfo10();

        $info->order_id = $this->product_information->product_id;
        $info->order_name = $this->product_information->product_name;
        $info->order_price = $this->product_information->product_price;
        $info->image_url = $this->product_information->image_url;

        return $info;
    }

    /**
     * @return FeeDetails
     */
    public function getFeeDetails()
    {
        return null;
    }

    /**
     * @return Instructions
     */
    public function getInstructions()
    {
        return $this->payment_instructions;
    }
}