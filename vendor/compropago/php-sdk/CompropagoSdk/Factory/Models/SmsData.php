<?php

namespace CompropagoSdk\Factory\Models;

class SmsData
{
    public $object;

    public function __construct()
    {
        $this->object = new SmsObject();
    }
}