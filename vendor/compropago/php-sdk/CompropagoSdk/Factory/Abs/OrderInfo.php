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

/**
 * Class OrderInfo
 * @package CompropagoSdk\Factory\Abs
 */
abstract class OrderInfo
{
    /**
     * @return string
     */
    public abstract function getOrderId();

    /**
     * @return string
     */
    public abstract function getOrderPrice();

    /**
     * @return string
     */
    public abstract function getOrderName();

    /**
     * @return string
     */
    public abstract function getPaymentMethod();

    /**
     * @return string
     */
    public abstract function getStore();

    /**
     * @return string
     */
    public abstract function getCountry();

    /**
     * @return string
     */
    public abstract function getImageUrl();

    /**
     * @return string
     */
    public abstract function getSuccessUrl();
}