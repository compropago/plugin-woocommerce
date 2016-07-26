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
use CompropagoSdk\Factory\Abs\Instructions;

class Instructions11 extends Instructions
{
    public $description;
    public $step_1;
    public $step_2;
    public $step_3;
    public $note_extra_comition;
    public $note_expiration_date;
    public $note_confirmation;
    public $details;

    public function __construct()
    {
        $this->details = new InstructionDetails11();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getStep1()
    {
        return $this->step_1;
    }

    /**
     * @return string
     */
    public function getStep2()
    {
        return $this->step_2;
    }

    /**
     * @return string
     */
    public function getStep3()
    {
        return $this->step_3;
    }

    /**
     * @return string
     */
    public function getNoteExtraComition()
    {
        return $this->note_extra_comition;
    }

    /**
     * @return string
     */
    public function getNoteExpirationDate()
    {
        return $this->note_expiration_date;
    }

    /**
     * @return string
     */
    public function getNoteConfirmation()
    {
        return $this->note_confirmation;
    }

    /**
     * @return InstrcutionDetails
     */
    public function getDetails()
    {
        return $this->details;
    }
}