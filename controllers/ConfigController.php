<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

require_once ABSPATH . "wp-load.php";
include_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once __DIR__ . "/../vendor/autoload.php";

use CompropagoSdk\Resources\Webhook;


class ConfigController
{
    private $response;
    private $data;
    private $retro;

    /**
     * ConfigController constructor.
     * @param array $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function save()
    {
        try {
            $this->__init__();
            $this->response = [
                'error'     => false,
                'message'   => __('Se guardaron correctamente las configuraciones.'),
                'retro'     => $this->retro
            ];
        } catch(\Exception $e) {
            $this->response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        return json_encode($this->response);
    }

    /**
     * Save al configuration for ComproPago Plugin
     * @throws Exception
     */
    private function __init__()
    {
        $pattern = '/_live_/';
        $mode = null;

        $pk_mode = preg_match($pattern, $this->data['publickey']);
        $sk_mode = preg_match($pattern, $this->data['privatekey']);

        if (isset($this->data['live'])) {
            $mode = $this->data['live'];
        } else if ($sk_mode && $pk_mode) {
            $mode = 'yes';
        } elseif (!$sk_mode && !$pk_mode) {
            $mode = 'no';
        } else {
            $message = __('Las llaves escritas pertenecen a modos distintos');
            throw new \Exception($message);
        }

        /**
         * Active Cash Payment
         */
        delete_option('woocommerce_cpcash_settings');
        add_option('woocommerce_cpcash_settings', array('enabled' => $this->data['cash_enable']));

        /**
         * Active SPEI Payment
         */
        delete_option('woocommerce_cpspei_settings');
        add_option('woocommerce_cpspei_settings', array('enabled' => $this->data['spei_enable']));

        /**
         * Cash Title
         */
        delete_option('compropago_cash_title');
        add_option('compropago_cash_title', $this->data['cash_title']);

        /**
         * Spei Title
         */
        delete_option('compropago_spei_title');
        add_option('compropago_spei_title', $this->data['spei_title']);

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
        add_option('compropago_live', $mode);

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

        /**
         * Active providers
         */
        delete_option('compropago_provallowed');
        add_option('compropago_provallowed', $this->data['provallowed']);

        $live = $mode == 'yes' ? true : false;

        try {
            $objWebhook = (new Webhook)->withKeys(
                $this->data['publickey'],
                $this->data['privatekey']
            );

            $webhook_id = get_option('compropago_webhook_id');
            if ($webhook_id) {
                # Delete old webhook
                $objWebhook->delete($webhook_id);
            }

            # Create webhook
            $this->response = $objWebhook->create( $this->data['webhook'] );

            /**
             * Webhook Id
             */
            delete_option('compropago_webhook_id');
            add_option('compropago_webhook_id', $this->response['id']);
        } catch (\Exception $e) {
            $errors = [
                'ComproPago: Request Error [409]: ',
            ];
            $message = json_decode(str_replace($errors, '', $e->getMessage()), true);
            
            # Ignore Webhook registered
            if ( isset($message['code']) && $message['code']==409 )
            {
                $this->messageManager->addError("ComproPago: {$message}");
            }
        }
    }
}

function cp_config()
{
    $config = new ConfigController($_POST);
    die($config->save());
}

add_action('rest_api_init', function () {
    register_rest_route(
        'compropago/',
        'config',
        array(
            'methods' => 'POST',
            'callback' => 'cp_config',
        )
    );
});
