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

class FeeDetails11 extends FeeDetails
{
    public $amount;
    public $currency;
    public $type;
    public $description;
    public $application;
    public $amount_refunded;
    public $tax;

    public function __construct()
    {
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function getAmountRefunded()
    {
        return $this->amount_refunded;
    }

    public function getTax()
    {
        return $this->tax;
    }
}