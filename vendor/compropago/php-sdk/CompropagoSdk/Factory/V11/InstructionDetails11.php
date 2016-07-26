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


use CompropagoSdk\Factory\Abs\InstrcutionDetails;

class InstructionDetails11 extends InstrcutionDetails
{
    public $amount;
    public $store;
    public $bank_account_number;
    public $bank_name;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @return string
     */
    public function getBankAccountNumber()
    {
        return $this->bank_account_number;
    }

    /**
     * @return string
     */
    public function getBankName()
    {
        return $this->bank_name;
    }
}