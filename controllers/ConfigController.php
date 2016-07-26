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
 * Compropago plugin-woocommerce
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 */

require_once __DIR__ . "/../../../../wp-load.php";
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once __DIR__ . "/../vendor/autoload.php";

use CompropagoSdk\Client;

class ConfigController
{

    private $response;
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
        try{
            $this->__init__();
            $this->response = array(
                'error' => false,
                'message' => 'Se guardaron correctamente las configuraciones.'
            );
        }catch(Exception $e){
            $this->response = array(
                'error' => true,
                'message' => $e->getMessage()
            );
        }

        header('Content-Type: application/json');
        echo json_encode($this->response);
    }

    private function __init__()
    {
        
        global $wpdb;

        /**
         * Publickey option
         */
        if(get_option('compropago_publickey')){
            add_option('compropago_publickey', $this->data['publickey']);
        }else{
            update_option('compropago_publickey', $this->data['publickey']);
        }

        /**
         * Private key option
         */
        if(get_option('compropago_privatekey')){
            add_option('compropago_privatekey', $this->data['privatekey']);
        }else{
            update_option('compropago_privatekey', $this->data['privatekey']);
        }

        /**
         * Live option
         */
        if(get_option('compropago_live')){
            add_option('compropago_live', $this->data['live']);
        }else{
            update_option('compropago_live', $this->data['live']);
        }

        /**
         * Showlogo option
         */
        if(get_option('compropago_showlogo')){
            add_option('compropago_showlogo', $this->data['showlogo']);
        }else{
            update_option('compropago_showlogo', $this->data['showlogo']);
        }

        $client = new Client(
            $this->data['publickey'],
            $this->data['privatekey'],
            ($this->data['live'] == 'yes') ? true : false
        );
        
        /**
         * Webhook option
         */

        if(get_option('compropago_webhook')){
            add_option('compropago_webhook', $this->data['webhook']);
            $webhook = $client->api->createWebhook($this->data['webhook']);

            $recordTime = time();

            $wpdb->insert($wpdb->prefix . 'compropago_webhook_transactions', array(
                    'webhookId' => $webhook->id,
                    'updated' => $recordTime,
                    'status' => $webhook->status
                )
            );
        }else{
            update_option('compropago_webhook', $this->data['webhook']);

            $last = "SELECT MAX(id) as 'last' FROM {$wpdb->prefix}compropago_webhook_transactions";

            $row_last = $wpdb->get_row($last);

            $recordTime = time();

            if(!empty($row_last->last)){
                $sql = "SELECT * FROM {$wpdb->prefix}compropago_webhook_transactions WHERE id = {$row_last->last}";

                if($row = $wpdb->get_row($sql)){
                    $webhook = $client->api->updateWebhook($row->webhookId, $this->data['webhook']);
                    $wpdb->insert($wpdb->prefix . 'compropago_webhook_transactions', array(
                            'webhookId' => $webhook->id,
                            'updated' => $recordTime,
                            'status' => $webhook->status
                        )
                    );
                }else{
                    throw new Exception('Error al recuperar la ultima transaccion del webhook');
                }
            }else{
                $webhook = $client->api->createWebhook($this->data['webhook']);
                $wpdb->insert($wpdb->prefix . 'compropago_webhook_transactions', array(
                        'webhookId' => $webhook->id,
                        'updated' => $recordTime,
                        'status' => $webhook->status
                    )
                );
            }
        }



        /**
         * Provallowed option
         */
        if(get_option('compropago_provallowed')){
            add_option('compropago_provallowed', $this->data['provallowed']);
        }else{
            update_option('compropago_provallowed', $this->data['provallowed']);
        }

        /**
         * Descripcion option
         */
        if(get_option('compropago_descripcion')){
            add_option('compropago_descripcion', $this->data['descripcion']);
        }else{
            update_option('compropago_descripcion', $this->data['descripcion']);
        }

        /**
         * instrucciones option
         */
        if(get_option('compropago_instrucciones')){
            add_option('compropago_instrucciones', $this->data['instrucciones']);
        }else{
            update_option('compropago_instrucciones', $this->data['instrucciones']);
        }
    }

}

if($_POST){
    new ConfigController($_POST);
}else{
    header("Content-Type: application/json");
    $json = array(
        'error' => true,
        'message' => 'Access denied'
    );
    echo json_encode($json);
}