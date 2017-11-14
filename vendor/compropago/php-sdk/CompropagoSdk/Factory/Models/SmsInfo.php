<?php

namespace CompropagoSdk\Factory\Models;

class SmsInfo
{
    public $type;
    public $object;
    public $data;

    public function __construct()
    {
        $this->data = new SmsData();
    }
}