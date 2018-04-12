<?php
/**
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 */

namespace CompropagoSdk\UnitTest;

require_once __DIR__ . '/../CompropagoSdk/Client.php';

use CompropagoSdk\Client;

Client::register_autoload();

use CompropagoSdk\Factory\Factory;
use CompropagoSdk\Factory\Models\CpOrderInfo;
use CompropagoSdk\Factory\Models\EvalAuthInfo;
use CompropagoSdk\Factory\Models\Provider;
use CompropagoSdk\Factory\Models\Webhook;
use CompropagoSdk\Tools\Validations;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    private $mode;
    private $limit;
    private $orderInfo;
    private $webhookUrl;
    private $phoneNumber;

    private $publicKey;
    private $privateKey;

    public function setUp()
    {
        $this->orderInfo = [
            'order_id' => 12,
            'order_name' => "M4 sdk php",
            'order_price' => 123.45,
            'customer_name' => "Eduardo Aguilar",
            'customer_email' => "asdr@compropago.com"
        ];

        $this->mode = false;
        $this->limit = 15000;
        $this->webhookUrl = 'https://prueba123.com/webhook';
        $this->phoneNumber = '5561463627';

        $this->publicKey = 'pk_test_638e8b14112423a086';
        $this->privateKey = 'sk_test_9c95e149614142822f';
    }

    public function testCreateClient()
    {
        $res = false;

        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);
            $res = true;
        } catch(\Exception $e) {
            echo "\n====>> testCreateClient: ".$e->getMessage()."\n";
        }

        $this->assertTrue($res);
    }

    public function testEvalAuth()
    {
        $res = false;
        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);
            $response = Validations::evalAuth($client);

            $res = $response instanceof EvalAuthInfo;
        } catch(\Exception $e) {
            echo "\n====>> testEvalAuth: ".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testProviders()
    {
        $res = false;
        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);
            $response = $client->api->listProviders();

            $res = $response[0] instanceof Provider;
        } catch(\Exception $e) {
            echo "\n====>> testProviders: ".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testDefaultProviders()
    {
        $res = false;
        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);
            $response = $client->api->listDefaultProviders();

            $res = ($response[0] instanceof Provider && sizeof($response) == 13);
        } catch (\Exception $e) {
            echo "\n====>> testDefaultProviders: ".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testProvidersLimit()
    {
        $flag = true;

        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);
            $res = $client->api->listProviders($this->limit);

            foreach ($res as $provider) {
                if ($provider->transaction_limit < $this->limit) {
                    $flag = false;
                    break;
                }
            }
        } catch(\Exception $e) {
            echo "\n====>> testProvidersLimit: ".$e->getMessage()."\n";
            $flag = false;
        }
        $this->assertTrue($flag);
    }

    public function testProvidersCurrency()
    {
        $flag = true;
        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);
            $provs = $client->api->listProviders(700, 'USD');

            foreach ($provs as $prov) {
                if ($prov->transaction_limit < $this->limit) {
                    $flag = false;
                    break;
                }
            }
        } catch(\Exception $e) {
            echo "\n====>> testProvidersCurrency: ".$e->getMessage()."\n";
            $flag = false;
        }
        $this->assertTrue($flag);
    }

    public function testPlaceOrder()
    {
        $res = false;
        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);
            $order = Factory::getInstanceOf('PlaceOrderInfo', $this->orderInfo);

            $response = $client->api->placeOrder($order);

            $res = !empty($response->id);
        } catch(\Exception $e) {
            echo "\n====>> testPlaceOrder:".$e->getMessage()."\n";
        }

        $this->assertTrue($res);
    }

    public function testPlaceOrderExpdate()
    {
        $res = false;
        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);

            $epoch = time() + (6 * 60 * 60);
            $this->orderInfo['expiration_time'] = $epoch;

            $order = Factory::getInstanceOf('PlaceOrderInfo', $this->orderInfo);
            $response = $client->api->placeOrder($order);

            $res = $epoch == $response->expires_at;
        } catch (\Exception $e) {
            echo "\n====>> testPlaceOrderExpdate: ".$e->getMessage() . "\n";
        }
        $this->assertTrue($res);
    }

    public function testVerifyOrder()
    {
        $res = false;
        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);
            $order = Factory::getInstanceOf('PlaceOrderInfo', $this->orderInfo);

            $order_aux = $client->api->placeOrder($order);
            $response = $client->api->verifyOrder($order_aux->id);

            $res = $response instanceof CpOrderInfo;
        } catch (\Exception $e) {
            echo "\n====>> testVerifyOrder: ".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testSms()
    {
        $res = false;
        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);
            $order = Factory::getInstanceOf('PlaceOrderInfo', $this->orderInfo);

            $order_aux = $client->api->placeOrder($order);
            $response = $client->api->sendSmsInstructions($this->phoneNumber, $order_aux->id);

            $res = !empty($response->type);
        } catch(\Exception $e) {
            echo "\n====>> testSms: ".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testListWebhooks()
    {
        $res = false;
        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);
            $webhooks = $client->api->listWebhooks();

            $res = is_array($webhooks) && ($webhooks[0] instanceof Webhook);
        } catch(\Exception $e) {
            echo "\n====>> testListWebhooks: ".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testCreateWebhook()
    {
        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);
            $response = $client->api->createWebhook($this->webhookUrl);

            $this->assertTrue(($response instanceof Webhook));
            return $response;
        } catch(\Exception $e) {
            echo "\n====>> testCreateWebhook: ".$e->getMessage()."\n";
            echo $e->getTraceAsString();
            $this->assertTrue(false);
            return null;
        }
    }

    /**
     * @depends testCreateWebhook
     * @param Webhook $webhook
     * @return Webhook
     */
    public function testUpdateWebhook(Webhook $webhook)
    {
        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);

            $webhookUrl = $this->webhookUrl . '/new';

            $response = $client->api->updateWebhook($webhook->id, $webhookUrl);

            $this->assertTrue(($response instanceof Webhook));
            return $response;
        } catch(\Exception $e) {
            echo "\n====>> testUpdateWebhook: ".$e->getMessage()."\n";
            echo $e->getTraceAsString();
            $this->assertTrue(false);
            return null;
        }
    }

    /**
     * @depends testUpdateWebhook
     * @param Webhook $webhook
     */
    public function testDeleteWebhook(Webhook $webhook)
    {
        try {
            $client = new Client($this->publicKey, $this->privateKey, $this->mode);
            $response = $client->api->deleteWebhook($webhook->id);

            $this->assertTrue(($response instanceof Webhook));
        } catch(\Exception $e) {
            echo "\n====>> testDeleteWebhook: ".$e->getMessage()."\n";
            echo $e->getTraceAsString();
            $this->assertTrue(false);
        }
    }
}