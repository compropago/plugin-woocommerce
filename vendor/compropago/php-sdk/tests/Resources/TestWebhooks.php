<?php

namespace Tests\Resources\Payments;

use PHPUnit\Framework\TestCase;
use CompropagoSdk\Resources\Webhook;

class TestWebhook extends TestCase
{
    const PUBLIC_KEY = 'pk_test_638e8b14112423a086';
    const PRIVATE_KEY = 'sk_test_9c95e149614142822f';

    const TESTWH = 'http://test.xyz/test';
    const U_TESTWH = 'http://test.xyz/test2';

    /**
     * Test create object of webhook
     *
     * @covers Webhook::withKeys
     *
     * @return Webhook
     */
    public function testCreateObject()
    {
        try {
            $obj = (new Webhook)->withKeys(self::PUBLIC_KEY, self::PRIVATE_KEY);
            $this->assertTrue($obj instanceof Webhook);

            return $obj;
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);

            return null;
        }
    }

    /**
     * Trest list webhooks
     *
     * @depends testCreateObject
     *
     * @covers Webhook::getAll
     *
     * @param Webhook $obj Instance of Webhook resource
     *
     * @return Webhook
     */
    public function testListWebhooks(Webhook $obj)
    {
        try {
            $list = $obj->getAll();
            $this->assertTrue(is_array($list));
            return $obj;
        } catch (\Esception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);

            return null;
        }
    }

    /**
     * Test webhook creation
     *
     * @depends testCreateObject
     *
     * @covers Webhook::create
     *
     * @param Webhook $obj Webhook instance
     *
     * @return array Webhook structure
     */
    public function testCreateWebhook(Webhook $obj)
    {
        try {
            $wh = $obj->create(self::TESTWH . rand(0, 100));
            $this->assertTrue(is_array($wh) && isset($wh['id']));

            return $wh;
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);

            return null;
        }
    }

    /**
     * Test webhook creation
     *
     * @depends testCreateObject
     * @depends testCreateWebhook
     *
     * @covers Webhook::update
     *
     * @param Webhook $obj webhook instance
     * @param array   $wh  Webhook structure
     *
     * @return array webhook structure
     */
    public function testUpdateWebhook(Webhook $obj, $wh)
    {
        try {
            $wh = $obj->update($wh['id'], self::U_TESTWH . rand(0, 1000));
            $this->assertTrue(is_array($wh) && isset($wh['id']));

            return $wh;
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);

            return null;
        }
    }

    /**
     * Test webhook creation
     *
     * @depends testCreateObject
     * @depends testUpdateWebhook
     *
     * @covers Webhook::delete
     *
     * @param Webhook $obj webhook instance
     * @param array   $wh  Webhook structure
     */
    public function testDeleteWebhook(Webhook $obj, $wh)
    {
        try {
            $wh = $obj->delete($wh['id']);
            $this->assertTrue(
                is_array($wh) && isset($wh['id']) && $wh['status'] == 'deleted'
            );
        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
            $this->assertTrue(false);
        }
    }
}
