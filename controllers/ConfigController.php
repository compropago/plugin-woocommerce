<?php

require_once __DIR__ . "/../../../../wp-load.php";
include_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once __DIR__ . "/../vendor/autoload.php";

use CompropagoSdk\Client;

class ConfigController
{
    private $response;
    private $data;
    private $retro;

    /**
     * ConfigController constructor.
     * @param array $data
     *
     * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
     */
    public function __construct($data)
    {
        $this->data = $data;

        try{
            $this->__init__();
            $this->response = [
                'error' => false,
                'message' => 'Se guardaron correctamente las configuraciones.',
                'retro' => $this->retro
            ];
        }catch(Exception $e){
            $this->response = [
                'error' => true,
                'message' => $e->getMessage()
            ];
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

        $mode = ($this->data['live'] == 'yes') ? true : false;

        $client = new Client(
            $this->data['publickey'],
            $this->data['privatekey'],
            $mode
        );
      
        /**
         * Webhook option
         */
        try {
            if (get_option('compropago_webhook')) {
                delete_option('compropago_webhook');
                add_option('compropago_webhook', $this->data['webhook']);
                $webhook = $client->api->createWebhook($this->data['webhook']);

                $recordTime = time();

                $wpdb->insert($wpdb->prefix . 'compropago_webhook_transactions',
                    [
                        'webhookId' => $webhook->id,
                        'webhookUrl' => $webhook->url,
                        'updated' => $recordTime,
                        'status' => $webhook->status
                    ]
                );
            } else {
                delete_option('compropago_webhook');
                add_option('compropago_webhook', $this->data['webhook']);

                $last = "SELECT MAX(id) as 'last' FROM {$wpdb->prefix}compropago_webhook_transactions";

                $row_last = $wpdb->get_row($last);

                $recordTime = time();

                $webhook = $client->api->createWebhook($this->data['webhook']);
                $wpdb->insert($wpdb->prefix . 'compropago_webhook_transactions',
                    [
                        'webhookId' => $webhook->id,
                        'webhookUrl' => $webhook->url,
                        'updated' => $recordTime,
                        'status' => $webhook->status
                    ]
                );

            }
        } catch (Exception $e) {
            if ($e->getMessage() != 'Error: conflict.urls.create') {
                throw new Exception($e);
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
    }
}

if($_POST){
    new ConfigController($_POST);
}else{
    header("Content-Type: application/json");

    $json = [
        'error' => true,
        'message' => 'Access denied'
    ];

    echo json_encode($json);
}