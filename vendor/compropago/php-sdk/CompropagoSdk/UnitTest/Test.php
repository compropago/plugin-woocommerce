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

require_once __DIR__. "/../../../../autoload.php";

use CompropagoSdk\Client;
use CompropagoSdk\Factory\Abs\CpOrderInfo;
use CompropagoSdk\Factory\Abs\NewOrderInfo;
use CompropagoSdk\Factory\Abs\SmsInfo;
use CompropagoSdk\Models\PlaceOrderInfo;
use CompropagoSdk\Models\Webhook;
use CompropagoSdk\Tools\Validations;

class Test extends \PHPUnit_Framework_TestCase
{
    private $publickey = "pk_test_5989d8209974e2d62";
    private $privatekey = "sk_test_6ff4e982253c44c42";
    private $mode = false;
    
    private $phonenumber = "5561463627";

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
            echo "\n".$e->getMessage()."\n";
        }

        return $client;
    }

    /**
     * @depends testCreateClient
     * @param Client $client
     * @return \CompropagoSdk\Models\EvalAuthInfo|null
     */
    public function testEvalAuth(Client $client)
    {
        $res = null;
        try{
            $res = Validations::evalAuth($client);
        }catch(\Exception $e){
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue(!empty($res));

        return $res;
    }

    /**
     * @depends testEvalAuth
     * @param $info
     */
    public function testEvalAuthClass($info)
    {
        $this->assertTrue(
            (get_class($info) == "CompropagoSdk\\Models\\EvalAuthInfo")
        );
    }
    

    /**
     * @depends testCreateClient
     * @param Client $client
     * @return array
     */
    public function testServiceProviders(Client $client)
    {
        try{
            $res = $client->api->listProviders();
        }catch(\Exception $e){
            $res = array();
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue(is_array($res) && !empty($res));

        return $res;
    }

    /**
     * @depends testCreateClient
     * @param Client $client
     */
    public function testServiceProvidersLimit(Client $client)
    {
        try{
            $res = $client->api->listProviders(false, 15000);

            $flag = true;
            foreach ($res as $provider){
                if($provider->transaction_limit < 15000){
                    $flag = false;
                    break;
                }
            }
        }catch(\Exception $e){
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue(isset($flag) && $flag);
    }

    /**
     * @depends testCreateClient
     * @param Client $client
     */
    public function testServiceProviderAuth(Client $client)
    {
        try{
            $res = $client->api->listProviders(true);

            if($res){
                $res = $client->api->listProviders(true);
            }
        }catch(\Exception $e){
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue(isset($res) && is_array($res) && !empty($res));
    }


    /**
     * @depends testCreateClient
     * @param Client $client
     */
    public function testServiceProviderAuthLimit(Client $client)
    {
        try{
            $res = $client->api->listProviders(true, 15000);

            $flag = true;
            foreach ($res as $provider){
                if($provider->transaction_limit < 15000){
                    $flag = false;
                    break;
                }
            }
        }catch(\Exception $e){
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue(isset($flag) && $flag);
    }

    /**
     * @depends testCreateClient
     * @param Client $client
     */
    public function testServiceProvidersAuthFetch(Client $client)
    {
        try{
            $res = $client->api->listProviders(true, 15000, true);

            $flag = true;
            foreach ($res as $provider){
                if($provider->transaction_limit < 15000){
                    $flag = false;
                    break;
                }
            }
        }catch(\Exception $e){
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue(isset($flag) && $flag);
    }
    

    /**
     * @depends testServiceProviders
     * @param array $providers
     * @return array
     */
    public function testEmptyArrayProviders(array $providers)
    {
        $this->assertTrue(!empty($providers));
        return $providers;
    }

    /**
     * @depends testEmptyArrayProviders
     * @param array $providers
     */
    public function testTypeArrayProviders(array $providers)
    {
        $flag = true;
        foreach($providers as $key => $value){
            $flag = (get_class($value) == "CompropagoSdk\\Models\\Provider") ? $flag : false;
            if(!$flag)
                break;
        }

        $this->assertTrue($flag);
    }

    /**
     * @depends testCreateClient
     * @param Client $client
     * @return NewOrderInfo
     */
    public function testServicePlaceOrder(Client $client)
    {
        try{
            $order = new PlaceOrderInfo("12","M4 Style",180,"Eduardo Aguilar","eduardo.aguilar@compropago.com");
            $res = $client->api->placeOrder($order);
        }catch(\Exception $e){
            $res = null;
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue(!empty($res));

        return $res;
    }

    /**
     * @depends testServicePlaceOrder
     * @param $neworder
     */
    public function testTypeServicePlaceOrder($neworder)
    {
        $this->assertTrue((get_parent_class($neworder) == "CompropagoSdk\\Factory\\Abs\\NewOrderInfo"));
    }

    /**
     * @depends testServicePlaceOrder
     * @param NewOrderInfo $order
     * @return CpOrderInfo
     */
    public function testServiceVerifyOrder(NewOrderInfo $order)
    {
        try {
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );
            $res = $client->api->verifyOrder($order->getId());
        } catch (\Exception $e) {
            $res = null;
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue(!empty($res));
        return $res;
    }

    /**
     * @depends testServiceVerifyOrder
     * @param CpOrderInfo $order
     */
    public function testTypeServiceVerifyOrder(CpOrderInfo $order)
    {
        $this->assertTrue((get_parent_class($order) == "CompropagoSdk\\Factory\\Abs\\CpOrderInfo"));
    }

    /**
     * @depends testServicePlaceOrder
     * @param NewOrderInfo $order
     * @return SmsInfo
     */
    public function testServiceSms(NewOrderInfo $order)
    {
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );

            $res = $client->api->sendSmsInstructions($this->phonenumber, $order->getId());
        }catch(\Exception $e){
            $res = null;
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue(!empty($res));
        return $res;
    }

    /**
     * @depends testServiceSms
     * @param SmsInfo $info
     */
    public function testTypeServiceSms(SmsInfo $info)
    {
        $this->assertTrue((get_parent_class($info) == "CompropagoSdk\\Factory\\Abs\\SmsInfo"));
    }

    /**
     * @depends testCreateClient
     * @param Client $client
     */
    public function testGetWebhooks(Client $client)
    {
        try{
            $res = $client->api->listWebhooks();
            if(is_array($res)){
                if(count($res) > 0 && get_class($res[0]) == "CompropagoSdk\\Models\\Webhook"){
                    $flag = true;
                }else{
                    $flag = false;
                }
            }else{
                $flag = false;
            }
        }catch(\Exception $e){
            echo "\n".$e->getMessage()."\n";
            $flag = false;
        }

        $this->assertTrue($flag);
    }

    /**
     * @depends testCreateClient
     * @param Client $client
     * @return Webhook | null
     */
    public function testCreateWebhook(Client $client)
    {
        $flag = false;
        $res = null;
        try{
            $res = $client->api->createWebhook("http://prueba.com");
            if(get_class($res) == "CompropagoSdk\\Models\\Webhook" &&
                ($res->status == 'new' || $res->status == 'exists')){
                $flag = true;
            }
        }catch(\Exception $e){
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue($flag);
        return $res;
    }

    /**
     * @depends testCreateWebhook
     * @param Webhook $webhook
     */
    public function testUpdateWebhook(Webhook $webhook)
    {
        $flag = false;
        $res = null;
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );

            $res = $client->api->updateWebhook($webhook->id, "prueba2.com");

            if(get_class($res) == "CompropagoSdk\\Models\\Webhook" && $res->status == 'updated'){
                $flag = true;
            }
        }catch(\Exception $e){
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue($flag);
    }

    /**
     * @depends testCreateWebhook
     * @param Webhook $webhook
     */
    public function testDeleteWebhook(Webhook $webhook)
    {
        $flag = false;
        $res = null;
        try{
            $client = new Client(
                $this->publickey,
                $this->privatekey,
                $this->mode
            );

            $res = $client->api->deleteWebhook($webhook->id);

            if(get_class($res) == "CompropagoSdk\\Models\\Webhook" && $res->status == 'deleted'){
                $flag = true;
            }
        }catch(\Exception $e){
            echo "\n".$e->getMessage()."\n";
        }

        $this->assertTrue($flag);
    }
}