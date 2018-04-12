<?php

namespace CompropagoSdk\UnitTest;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use CompropagoSdk\Tools\Request as HttpReq;

class TestRequest extends TestCase
{
    public function testGET()
    {
        try {
            $url = 'https://api.compropago.com/v1/providers/true';
            $res = HttpReq::get($url);

            if (!empty($res->body) && !empty($res->headers) && is_array($res->cookies) && is_numeric($res->statusCode)){
                $this->assertTrue(true);
                return;
            }

            $this->assertTrue(false);
            return;
        } catch(\Exception $e) {
            echo "\n====>> GET: {$e->getMessage()}\n";
            echo $e->getTraceAsString();
            $this->assertTrue(false);
        }

        return;
    }

    public function testPOST()
    {
        $url = 'https://api.compropago.com/v1/charges';

        $auth = [
            'user' => 'sk_test_56e31883637446b1b',
            'pass' => 'pk_test_8781245a88240f9cf'
        ];

        $data = [
            'order_id' => 12,
            'order_name' => "M4 sdk php",
            'order_price' => 123.45,
            'customer_name' => "Eduardo Aguilar",
            'customer_email' => "asdr@compropago.com",
            'payment_type' => 'OXXO'
        ];

        try {
            $res = HttpReq::post($url, $data, array(), $auth);

            if (!empty($res->body) && !empty($res->headers) && is_array($res->cookies) && is_numeric($res->statusCode)){
                $this->assertTrue(true);
                return;
            }

            $this->assertTrue(false);
            return;
        } catch(\Exception $e) {
            echo "\n====>> POST: {$e->getMessage()}\n";
            echo $e->getTraceAsString();
            $this->assertTrue(false);
        }

        return;
    }
}