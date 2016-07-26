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


namespace CompropagoSdk\Factory\Abs;


abstract class CpOrderInfo
{
    /**
     * @return string
     */
    public abstract function getId();

    /**
     * @return string
     */
    public abstract function getType();

    /**
     * @return string
     */
    public abstract function getCreated();

    /**
     * @return bool
     */
    public abstract function getPaid();

    /**
     * @return string
     */
    public abstract function getAmount();

    /**
     * @return string
     */
    public abstract function getCurrency();

    /**
     * @return bool
     */
    public abstract function getRefunded();

    /**
     * @return string
     */
    public abstract function getFee();

    /**
     * @return \CompropagoSdk\Factory\Abs\FeeDetails
     */
    public abstract function getFeeDetails();

    /**
     * @return \CompropagoSdk\Factory\Abs\OrderInfo
     */
    public abstract function getOrderInfo();

    /**
     * @return \CompropagoSdk\Models\Customer
     */
    public abstract function getCustomer();

    /**
     * @return string
     */
    public abstract function getCaptured();

    /**
     * @return string
     */
    public abstract function getFailureMessage();

    /**
     * @return string
     */
    public abstract function getFailureCode();

    /**
     * @return double
     */
    public abstract function getAmountRefunded();

    /**
     * @return string
     */
    public abstract function getDescription();

    /**
     * @return string
     */
    public abstract function getDispute();
}