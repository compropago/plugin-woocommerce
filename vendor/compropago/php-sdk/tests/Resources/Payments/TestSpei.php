<?php

namespace Tests\Resources\Payments;

use PHPUnit\Framework\TestCase;
use CompropagoSdk\Resources\Payments\Spei;

class TestSpei extends TestCase
{
    const PUBLIC_KEY = 'pk_test_638e8b14112423a086';
    const PRIVATE_KEY = 'sk_test_9c95e149614142822f';

    /**
     * Test creation of object Spei
     *
     * @covers Spei::withKeys
     *
     * @return Spei Instance of Spei object
     */
    public function testCreateObject()
    {
        try {
            $obj = (new Spei)->withKeys(self::PUBLIC_KEY, self::PRIVATE_KEY);
            $this->assertTrue($obj instanceof Spei);

            return $obj;
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);

            return null;
        }
    }

    /**
     * Test spei order creation
     *
     * @depends testCreateObject
     *
     * @covers Spei::createOrder
     *
     * @param Spei $obj Instance of Spei object
     *
     * @return array New Spei order
     */
    public function testCreateOrder(Spei $obj)
    {
        try {
            $data = [
                "product" => [
                    "id" => "12",
                    "price" => 123.45,
                    "name" => "test order spei",
                    "url" => "",
                    "currency" => "MXN"
                ],
                "customer" => [
                    "name" => "Eduardo Aguilar",
                    "email" => "devenv" . rand(0, 100) . "@compropago.com",
                    "phone" => ""
                ],
                "payment" =>  [
                    "type" => "SPEI"
                ]
            ];

            $order = $obj->createOrder($data);
            $this->assertTrue(is_array($order) && isset($order['data']['id']));
            return $order;
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);

            return null;
        }
    }

    /**
     * Test spei order verification
     *
     * @depends testCreateObject
     * @depends testCreateOrder
     *
     * @covers Spei::verifyOrder
     *
     * @param Spei $obj
     *
     * @param array $order
     */
    public function testVerifyOrder(Spei $obj, $order)
    {
        try {
            $verified = $obj->verifyOrder($order['data']['id']);
            $this->assertTrue($order['data']['id'] === $verified['data']['id']);
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);
        }
    }
}
