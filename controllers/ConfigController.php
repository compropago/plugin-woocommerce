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
  private $retro;

  public function __construct($data)
  {
    $this->data = $data;
    try{
      $this->__init__();
      $this->response = array(
        'error' => false,
        'message' => 'Se guardaron correctamente las configuraciones.',
        'retro' => $this->retro
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
     * Active Plugin
     */
    delete_option('woocommerce_compropago_settings');
    add_option('woocommerce_compropago_settings', array('enabled' => $this->data['enabled']));

    /**
     * Publickey option
     */
    delete_option('compropago_publickey');
    add_option('compropago_publickey', $this->data['publickey']);

    /**
     * Private key option
     */
    delete_option('compropago_privatekey');
    add_option('compropago_privatekey', $this->data['privatekey']);

    /**
     * Live option
     */
    delete_option('compropago_live');
    add_option('compropago_live', $this->data['live']);

    /**
     * Showlogo option
     */
    delete_option('compropago_showlogo');
    add_option('compropago_showlogo', $this->data['showlogo']);

    /**
     * Title option
     */
    delete_option('compropago_title');
    add_option('compropago_title', $this->data['title']);

    /**
     * Completed Order
     */
    delete_option('compropago_completed_order');
    add_option('compropago_completed_order', $this->data['complete_order']);

    /**
     * Initial state
     */
    delete_option('compropago_initial_state');
    add_option('compropago_initial_state', $this->data['initial_state']);

    /**
     * Debug mode
     */
    delete_option('compropago_debug');
    add_option('compropago_debug', $this->data['debug']);


    $client = new Client(
      $this->data['publickey'],
      $this->data['privatekey'],
      ($this->data['live'] == 'yes') ? true : false
    );
      
    /**
     * Webhook option
     */
    if(get_option('compropago_webhook')){
      delete_option('compropago_webhook');
      add_option('compropago_webhook', $this->data['webhook']);
      $webhook = $client->api->createWebhook($this->data['webhook']);

      $recordTime = time();

      $wpdb->insert($wpdb->prefix . 'compropago_webhook_transactions', 
        array(
          'webhookId' => $webhook->id,
          'webhookUrl' => $webhook->url,
          'updated' => $recordTime,
          'status' => $webhook->status
        )
      );
    }else{
      delete_option('compropago_webhook');
      add_option('compropago_webhook', $this->data['webhook']);

      $last = "SELECT MAX(id) as 'last' FROM {$wpdb->prefix}compropago_webhook_transactions";

      $row_last = $wpdb->get_row($last);

      $recordTime = time();

      if(!empty($row_last->last)){
        $sql = "SELECT * FROM {$wpdb->prefix}compropago_webhook_transactions WHERE id = {$row_last->last}";

        if($row = $wpdb->get_row($sql)){
          $webhook = $client->api->updateWebhook($row->webhookId, $this->data['webhook']);
          $wpdb->insert($wpdb->prefix . 'compropago_webhook_transactions', 
            array(
              'webhookId' => $webhook->id,
              'webhookUrl' => $webhook->url,
              'updated' => $recordTime,
              'status' => $webhook->status
            )
          );
        }else{
          throw new Exception('Error al recuperar la ultima transaccion del webhook');
        }
      }else{
        $webhook = $client->api->createWebhook($this->data['webhook']);
        $wpdb->insert($wpdb->prefix . 'compropago_webhook_transactions', 
          array(
            'webhookId' => $webhook->id,
            'webhookUrl' => $webhook->url,
            'updated' => $recordTime,
            'status' => $webhook->status
          )
        );
      }
    }

    /**
     * Provallowed option
     */
    delete_option('compropago_provallowed');
    add_option('compropago_provallowed', $this->data['provallowed']);

    /**
     * Descripcion option
     */
    delete_option('compropago_descripcion');
    add_option('compropago_descripcion', $this->data['descripcion']);


    /**
     * instrucciones option
     */
    delete_option('compropago_instrucciones');
    add_option('compropago_instrucciones', $this->data['instrucciones']);


    $this->retro = Utils::retroalimentacion(
      $this->data['publickey'],
      $this->data['privatekey'],
      ($this->data['live'] == 'yes'),
      get_option('woocommerce_compropago_settings')
    );

    delete_option('compropago_glocation');
    add_option('compropago_glocation', $this->data['glocation']);
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