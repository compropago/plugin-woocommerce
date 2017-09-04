<?php
/**
 * Copyright 2015 Compropago.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Compropago php-sdk
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 */

namespace CompropagoSdk\UnitTest;

require_once 'autoload.php';

use CompropagoSdk\Client;
use CompropagoSdk\Factory\Factory;
use CompropagoSdk\Factory\Models\CpOrderInfo;
use CompropagoSdk\Factory\Models\EvalAuthInfo;
use CompropagoSdk\Factory\Models\Provider;
use CompropagoSdk\Factory\Models\Webhook;
use CompropagoSdk\Tools\Validations;

class Test extends \PHPUnit_Framework_TestCase
{
    private $publickey  = "pk_test_638e8b14112423a086";
    private $privatekey = "sk_test_9c95e149614142822f";
    private $mode = false;
    
    private $phonenumber = "5561463627";
    private $limit = 15000;

    private $order_info = [
        'order_id' => 12,
        'order_name' => "M4 sdk php",
        'order_price' => 123.45,
        'customer_name' => "Eduardo Aguilar",
        'customer_email' => "asdr@compropago.com"
    ];

    public function testCreateClient()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $res = true;
        } catch(\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testEvalAuth()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $response = Validations::evalAuth($client);

            $res = $response instanceof EvalAuthInfo;
        } catch(\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testProviders()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $response = $client->api->listProviders();

            $res = $response[0] instanceof Provider;
        } catch(\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testDefaultProviders()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $response = $client->api->listDefaultProviders();

            $res = is_array($response);
        } catch (\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testProvidersLimit()
    {
        $flag = true;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $res = $client->api->listProviders($this->limit);

            foreach ($res as $provider) {
                if ($provider->transaction_limit < $this->limit) {
                    $flag = false;
                    break;
                }
            }
        } catch(\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
            $flag = false;
        }
        $this->assertTrue($flag);
    }

    public function testProvidersCurrency()
    {
        $flag = true;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $provs = $client->api->listProviders(700, 'USD');

            foreach ($provs as $key => $prov) {
                if ($prov->transaction_limit < $this->limit) {
                    $flag = false;
                    break;
                }
            }
        } catch(\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
            $flag = false;
        }
        $this->assertTrue($flag);
    }

    public function testPlaceOrder()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $order = Factory::getInstanceOf('PlaceOrderInfo', $this->order_info);

            $response = $client->api->placeOrder($order);

            $res = !empty($response->id);
        } catch(\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
        }

        $this->assertTrue($res);
    }

    public function testPlaceOrderExpdate()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);

            $epoch = time() + (6 * 60 * 60);
            $this->order_info['expiration_time'] = $epoch;

            $order = Factory::getInstanceOf('PlaceOrderInfo', $this->order_info);
            $response = $client->api->placeOrder($order);

            $res = $epoch == $response->expires_at;
        } catch (\Exception $e) {
            echo "====>> ".$e->getMessage();
        }
        $this->assertTrue($res);
    }

    public function testVerifyOrder()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $order = Factory::getInstanceOf('PlaceOrderInfo', $this->order_info);

            $order_aux = $client->api->placeOrder($order);
            $response = $client->api->verifyOrder($order_aux->id);

            $res = $response instanceof CpOrderInfo && !empty($response->id);
        } catch (\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testSms()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $order = Factory::getInstanceOf('PlaceOrderInfo', $this->order_info);

            $order_aux = $client->api->placeOrder($order);
            $response = $client->api->sendSmsInstructions($this->phonenumber, $order_aux->id);

            $res = !empty($response->type);
        } catch(\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testListWebhooks()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $webhooks = $client->api->listWebhooks();

            $res = is_array($webhooks) && ($webhooks[0] instanceof Webhook);
        } catch(\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
        }
        $this->assertTrue($res);
    }

    public function testCreateWebhook()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $response = $client->api->createWebhook("http://prueba.com");

            $res = $response instanceof Webhook;
        } catch(\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
        }

        $this->assertTrue($res);
    }

    public function testUpdateWebhook()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $webhook = $client->api->createWebhook("http://prueba.com");

            $response = $client->api->updateWebhook($webhook->id, "http://prueba2.com");

            $res = $response instanceof Webhook;
        } catch(\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
        }

        $this->assertTrue($res);
    }

    public function testDeactiveWebhook()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);

            $webhookId = "8b1f9725-54c5-4733-994b-b1e0f9c50baa";
            $webhook = $client->api->deactiveWebhook($webhookId);

            $res = $webhook->status == 'deactivated';
        } catch (\Exception $e) {
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue($res);
    }

    public function testDeleteWebhook()
    {
        $res = false;
        try {
            $client = new Client($this->publickey, $this->privatekey, $this->mode);
            $webhook = $client->api->createWebhook("http://prueba2.com");
            $response = $client->api->deleteWebhook($webhook->id);

            $res = $response instanceof Webhook;
        } catch(\Exception $e) {
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue($res);
    }
}