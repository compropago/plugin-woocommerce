<?php

namespace CompropagoSdk\Factory\Models;

/**
 * Class SmsInfo
 * @package CompropagoSdk\Factory\Models
 *
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */
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