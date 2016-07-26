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


class CpObject
{
    public $id;
    public $object;
    public $created_at;
    public $paid;
    public $amount;
    public $currency;
    public $refunded;
    public $fee;
    public $fee_details;
    public $payment_details;
    public $captured;
    public $failure_message;
    public $failure_code;
    public $amount_refunded;
    public $description;
    public $dispute;

    public function __construct()
    {
        $this->fee_details = new FeeDetails10();
        $this->payment_details = new PaymentDetails();
    }
}