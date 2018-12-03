<?php

namespace Tests\Resources\Payments;

use PHPUnit\Framework\TestCase;
use CompropagoSdk\Resources\Payments\Cash;

class TestCash extends TestCase
{
    const PUBLIC_KEY = 'pk_test_638e8b14112423a086';
    const PRIVATE_KEY = 'sk_test_9c95e149614142822f';

    /**
     * Test Cash object generation
     *
     * @covers Cash::withKeys
     *
     * @return Cash
     */
    public function testCreateObject()
    {
        try {
            $obj = (new Cash)->withKeys(self::PUBLIC_KEY, self::PRIVATE_KEY);
            $this->assertTrue($obj instanceof Cash);

            return $obj;
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);

            return null;
        }
    }

    /**
     * Test if list default providers is a valid array
     *
     * @depends testCreateObject
     *
     * @covers Cash::getDefaultProviders
     *
     * @param Cash $obj Instance of Cash object
     */
    public function testGetDefaultProviders(Cash $obj)
    {
        try {
            $providers = $obj->getDefaultProviders();
            $this->assertTrue(is_array($providers));
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);
        }
    }

    /**
     * Test if the list of providers for a store is a valid array
     *
     * @depends testCreateObject
     *
     * @covers Cash::getProviders
     *
     * @param Cash $obj Instance of Cash object
     */
    public function testGetProviders(Cash $obj)
    {
        try {
            $providers = $obj->getProviders();
            $this->assertTrue(is_array($providers));
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);
        }
    }

    /**
     * Test order creation for cash payment method
     *
     * @depends testCreateObject
     *
     * @covers Cash::createOrder
     *
     * @param Cash $obj Instance of Cash object
     */
    public function testCreateOrder(Cash $obj)
    {
        try {
            $data = [
                'order_id' => 1,
                'order_name' => 'Test order',
                'order_price' => 123.45,
                'customer_name' => 'Eduardo Aguilar',
                'customer_email' => 'devenv' . rand(0, 100) . '@compropago.com',
                'currency' => 'MXN',
                'payment_type' => 'OXXO',
                'image_url' => null
            ];

            $order = $obj->createOrder($data);

            $this->assertTrue(is_array($order));

            return $order;
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);

            return null;
        }
    }

    /**
     * Test order verification
     *
     * @depends testCreateObject
     * @depends testCreateOrder
     *
     * @covers Cash::verifyOrder
     *
     * @param Cash  $obj   Instance of Cash object
     * @param array $order Order array
     */
    public function testVerifyOrder(Cash $obj, $order)
    {
        try {
            $verified = $obj->verifyOrder($order['id']);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);
        }
    }
}
