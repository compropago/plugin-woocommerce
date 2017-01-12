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
use CompropagoSdk\Tools\Validations;

class Test extends \PHPUnit_Framework_TestCase
{
    private $publickey  = "pk_test_638e8b14112423a086";
    private $privatekey = "sk_test_9c95e149614142822f";
    private $mode = false;
    
    private $phonenumber = "5561463627";

    private $order_info = [
        'order_id' => 12,
        'order_name' => "M4 sdk php",
        'order_price' => 123.45,
        'customer_name' => "Eduardo Aguilar",
        'customer_email' => "asdr@compropago.com"
    ];

    public function testCreateClient()
    {
        $client = null;
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );
            $this->assertTrue(!empty($client));
        }catch(\Exception $e){
            $this->assertTrue(!empty($client));
            echo "====>>".$e->getMessage()."\n";
        }

        return $client;
    }

    public function testEvalAuth()
    {
        $res = null;
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );
            $res = Validations::evalAuth($client);
        }catch(\Exception $e){
            echo "====>>".$e->getMessage()."\n";
        }

        $this->assertTrue(!empty($res));
    }

    public function testServiceProviders()
    {
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );
            $res = $client->api->listProviders();
        }catch(\Exception $e){
            $res = array();
            echo "====>>".$e->getMessage()."\n";
        }

        $this->assertTrue(is_array($res) && !empty($res));
    }

    public function testServiceProvidersLimit()
    {
        $flag = true;
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );
            $res = $client->api->listProviders(false, 15000);

            foreach ($res as $provider){
                if($provider->transaction_limit < 15000){
                    $flag = false;
                    break;
                }
            }
        }catch(\Exception $e){
            echo "====>>".$e->getMessage()."\n";
            $flag = false;
        }

        $this->assertTrue($flag);
    }

    public function testServiceProvidersCurrency()
    {
        $flag = true;
        try {
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );
            $provs = $client->api->listProviders(true, 700, 'USD');

            foreach ($provs as $prov) {
                if ($prov->transaction_limit < 15000) {
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

    public function testServiceProviderAuth()
    {
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );
            $res = $client->api->listProviders(true);

            if($res){
                $res = $client->api->listProviders(true);
            }
        }catch(\Exception $e){
            echo "====>>".$e->getMessage()."\n";
        }

        $this->assertTrue(isset($res) && is_array($res) && !empty($res));
    }

    public function testServiceProviderAuthLimit()
    {
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );
            $res = $client->api->listProviders(true, 15000);

            $flag = true;
            foreach ($res as $provider){
                if($provider->transaction_limit < 15000){
                    $flag = false;
                    break;
                }
            }
        }catch(\Exception $e){
            echo "====>>".$e->getMessage()."\n";
        }

        $this->assertTrue(isset($flag) && $flag);
    }

    public function testServicePlaceOrder()
    {
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );
            $order = Factory::getInstanceOf('PlaceOrderInfo', $this->order_info);
            $res = $client->api->placeOrder($order);
        }catch(\Exception $e){
            echo "====>>".$e->getMessage()."\n";
        }

        $this->assertTrue(!empty($res));
    }

    public function testServiceVerifyOrder()
    {
        try {
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );

            $order = Factory::getInstanceOf('PlaceOrderInfo', $this->order_info);
            $order_aux = $client->api->placeOrder($order);

            $res = $client->api->verifyOrder($order_aux->id);
        } catch (\Exception $e) {
            echo "====>>".$e->getMessage()."\n";
        }

        $this->assertTrue(!empty($res));
    }

    public function testServiceSms()
    {
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );

            $order = Factory::getInstanceOf('PlaceOrderInfo', $this->order_info);
            $order_aux = $client->api->placeOrder($order);

            $res = $client->api->sendSmsInstructions($this->phonenumber, $order_aux->id);
        }catch(\Exception $e){
            echo "====>>".$e->getMessage()."\n";
        }

        $this->assertTrue(!empty($res));
    }

    public function testListWebhooks()
    {
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );
            $res = $client->api->listWebhooks();
            if(is_array($res)){
                if(count($res) > 0 && get_class($res[0]) == "CompropagoSdk\\Factory\\Models\\Webhook"){
                    $flag = true;
                }else{
                    $flag = false;
                }
            }else{
                $flag = false;
            }
        }catch(\Exception $e){
            echo "====>>".$e->getMessage()."\n";
            $flag = false;
        }

        $this->assertTrue($flag);
    }

    public function testCreateWebhook()
    {
        $flag = false;
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );
            $res = $client->api->createWebhook("http://prueba.com");

            $flag = (get_class($res) == "CompropagoSdk\\Factory\\Models\\Webhook");
        }catch(\Exception $e){
            echo "====>>".$e->getMessage()."\n";
        }

        $this->assertTrue($flag);
    }

    public function testUpdateWebhook()
    {
        $flag = false;
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );

            $webhook = $client->api->createWebhook("http://prueba.com");

            $res = $client->api->updateWebhook($webhook->id, "http://prueba2.com");

            $flag = (get_class($res) == "CompropagoSdk\\Factory\\Models\\Webhook");
        }catch(\Exception $e){
            echo "====>>".$e->getMessage()."\n";
        }

        $this->assertTrue($flag);
    }

    public function testDeleteWebhook()
    {
        $flag = false;
        $res = null;
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );

            $webhook = $client->api->createWebhook("http://prueba2.com");
            $res = $client->api->deleteWebhook($webhook->id);

            $flag = (get_class($res) == "CompropagoSdk\\Factory\\Models\\Webhook");
        }catch(\Exception $e){
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue($flag);
    }
}