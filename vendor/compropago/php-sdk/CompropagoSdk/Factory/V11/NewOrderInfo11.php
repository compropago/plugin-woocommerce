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


use CompropagoSdk\Factory\Abs\FeeDetails;
use CompropagoSdk\Factory\Abs\Instructions;
use CompropagoSdk\Factory\Abs\NewOrderInfo;
use CompropagoSdk\Factory\Abs\OrderInfo;

class NewOrderInfo11 extends NewOrderInfo
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
        $this->order_info = new OrderInfo11();
        $this->fee_details = new FeeDetails11();
        $this->instructions = new Instructions11();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getShortId()
    {
        return $this->short_id;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->exp_date;
    }

    /**
     * @return OrderInfo
     */
    public function getOrderInfo()
    {
        return $this->order_info;
    }

    /**
     * @return FeeDetails
     */
    public function getFeeDetails()
    {
        return $this->fee_details;
    }

    /**
     * @return Instructions
     */
    public function getInstructions()
    {
        return $this->instructions;
    }
}