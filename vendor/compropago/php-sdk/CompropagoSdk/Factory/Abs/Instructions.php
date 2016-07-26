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


abstract class Instructions
{
    /**
     * @return string
     */
    public abstract function getDescription();

    /**
     * @return string
     */
    public abstract function getStep1();

    /**
     * @return string
     */
    public abstract function getStep2();

    /**
     * @return string
     */
    public abstract function getStep3();

    /**
     * @return string
     */
    public abstract function getNoteExtraComition();

    /**
     * @return string
     */
    public abstract function getNoteExpirationDate();

    /**
     * @return string
     */
    public abstract function getNoteConfirmation();

    /**
     * @return InstrcutionDetails
     */
    public abstract function getDetails();
}