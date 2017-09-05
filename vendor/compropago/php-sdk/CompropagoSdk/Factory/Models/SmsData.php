<?php

namespace CompropagoSdk\Factory\Models;

/**
 * Class SmsData
 * @package CompropagoSdk\Factory\Models
 *
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */
class SmsData
{
    public $object;

    public function __construct()
    {
        $this->object = new SmsObject();
    }
}