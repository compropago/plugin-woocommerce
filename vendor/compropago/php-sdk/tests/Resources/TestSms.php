<?php

namespace Tests\Resources\Payments;

use PHPUnit\Framework\TestCase;
use CompropagoSdk\Resources\Sms;
use CompropagoSdk\Resources\Payments\Cash;
use CompropagoSdk\Resources\Payments\Spei;

class TestSms extends TestCase
{
    const PUBLIC_KEY = 'pk_test_638e8b14112423a086';
    const PRIVATE_KEY = 'sk_test_9c95e149614142822f';

    const PHONE = "5561463627";

    /**
     * Test creation object of Sms
     *
     * @covers Sms::withKeys
     *
     * @return Sms Instance of Sms object
     */
    public function testCreateObject()
    {
        try {
            $obj = (new Sms)->withKeys(self::PUBLIC_KEY, self::PRIVATE_KEY);
            $this->assertTrue($obj instanceof Sms);

            return $obj;
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);

            return null;
        }
    }

    /**
     * Test send SMS for a cash order
     *
     * @depends testCreateObject
     *
     * @covers Sms::sendToOrder
     *
     * @param Sms $obj Instance of Sms object
     */
    public function testSmsSendForCashOrders(Sms $obj)
    {
        try {
            $order = $this->createCashOrder();

            $sms = $obj->sendToOrder($order['id'], self::PHONE);

            $this->assertEquals('sms.success', $sms['type']);
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);
        }
    }

    /**
     * Test send SMS for a cash order
     *
     * @depends testCreateObject
     *
     * @covers Sms::sendToOrder
     *
     * @param Sms $obj Instance of Sms object
     */
    public function testSmsSendForSpeiOrders(Sms $obj)
    {
        try {
            $order = $this->createSpeiOrder();

            $sms = $obj->sendToOrder($order['data']['id'], self::PHONE);

            $this->assertEquals('sms.success', $sms['type']);
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);
        }
    }

    /**
     * Create a cash order
     *
     * @codeCoverageIgnore
     *
     * @return array
     */
    private function createCashOrder()
    {
        $cash = (new Cash)->withKeys(self::PRIVATE_KEY, self::PUBLIC_KEY);

        $data = [
            'order_id' => 2,
            'order_name' => 'Test order',
            'order_price' => 123.46,
            'customer_name' => 'Eduardo Aguilar',
            'customer_email' => 'devenv' . rand(0, 100) . '@compropago.com',
            'currency' => 'MXN',
            'payment_type' => 'OXXO',
            'image_url' => null
        ];

        return $cash->createOrder($data);
    }

    /**
     * Create a spei order
     *
     * @codeCoverageIgnore
     *
     * @return array
     */
    private function createSpeiOrder()
    {
        $spei = (new Spei)->withKeys(self::PRIVATE_KEY, self::PUBLIC_KEY);

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

        return $spei->createOrder($data);
    }
}
