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


use CompropagoSdk\Factory\Abs\SmsInfo;

class SmsInfo10 extends SmsInfo
{
    public $type;
    public $object;
    public $payment;

    public function __construct()
    {
        $this->payment = new SmsPayment();
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
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->payment->id;
    }

    /**
     * @return string
     */
    public function getShortId()
    {
        return $this->payment->short_id;
    }
}