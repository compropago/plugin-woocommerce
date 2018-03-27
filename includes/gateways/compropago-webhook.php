<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

use CompropagoSdk\Factory\Factory;
use CompropagoSdk\Client;
use CompropagoSdk\Tools\Validations;

/**
 * Main webhook function
 */
function compropago_webhook() {
    global $wpdb;

    try {
        $request = @file_get_contents('php://input');

        $orderInfo = Factory::getInstanceOf('CpOrderInfo', $request);

        if (empty($orderInfo) || empty($orderInfo->id)) {
            die(json_encode([
                'status' => 'error',
                'message' => 'Invalid request',
                'short_id' => null,
                'reference' => null
            ]));
        }

        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            die(json_encode([
                'status' => 'error',
                'message' => 'WooCommerce is not loaded',
                'short_id' => null,
                'reference' => null
            ]));
        }

        $config = get_option('woocommerce_compropago_settings');
        if($config['enabled'] != 'yes'){
            die(json_encode([
                'status' => 'error',
                'message' => 'ComproPago is not active',
                'short_id' => null,
                'reference' => null
            ]));
        }

        $publickey      = get_option('compropago_publickey');
        $privatekey     = get_option('compropago_privatekey');
        $live           = get_option('compropago_live') == 'yes' ? true : false;
        $complete_order = get_option('compropago_completed_order');
        $pending_status = get_option('compropago_initial_state');

        die(json_encode(['status' => $pending_status]));

        if (empty($publickey) || empty($privatekey)){
            die(json_encode([
                'status' => 'error',
                'message' => 'Invalid plugin credentials',
                'short_id' => null,
                'reference' => null
            ]));
        }

        $client = new Client($publickey, $privatekey, $live);

        Validations::validateGateway($client);

        if($orderInfo->short_id == "000000"){
            die(json_encode([
                'status' => 'success',
                'message' => 'Test webhook - OK',
                'short_id' => null,
                'reference' => null
            ]));
        }

        $verifyInfo = $client->api->verifyOrder($orderInfo->id);

        if($verifyInfo->type == 'error'){
            die(json_encode([
                'status' => 'error',
                'message' => 'Error al verificar la orden',
                'short_id' => null,
                'reference' => null
            ]));
        }

        if(!$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_orders'") ||
            !$wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix ."compropago_transactions'")
        ){
            die(json_encode([
                'status' => 'error',
                'message' => 'ComproPago tables not found',
                'short_id' => null,
                'reference' => null
            ]));
        }

        $query = "SELECT * FROM ".$wpdb->prefix."compropago_orders WHERE compropagoId = '".$verifyInfo->id."' ";

        if ($row = $wpdb->get_row($query)) {
            $id_order = intval($row->storeOrderId);
            $recordTime = time();

            $order = new WC_Order($id_order);

            switch($verifyInfo->type){
                case 'change.success':
                    if ($complete_order == 'fin') {
                        $order->payment_complete();
                    } else {
                        $order->update_status('processing', __( 'ComproPago - Payment Confirmed', 'compropago' ));
                        $new_status = 'processing';
                    }
                    break;

                case 'change.pending':
                    $order->update_status(get_option('compropago_initial_state'), __( 'ComproPago - Pending Payment', 'compropago' ));
                    $new_status = get_option('compropago_initial_state');
                    break;

                case 'change.expired':
                    $order->update_status('cancelled', __( 'ComproPago - Expired', 'compropago' ));
                    $new_status = 'cancelled';
                    break;

                default:
                    $order->update_status('on-hold', __( 'ComproPago - On Hold', 'compropago' ));
                    $new_status = 'on-hold';
            }

            if (!empty($new_status)) {
                $order->set_status($new_status);
            }

            $order->save();

            $sql = "UPDATE `".$wpdb->prefix."compropago_orders` SET 
            `modified` = '".$recordTime."',
		    `compropagoStatus` = '".$verifyInfo->type."', 
		    `storeExtra` = '".$verifyInfo->type."' WHERE `id` = '".$row->id."'";

            if(!$wpdb->query($sql)){
                die(json_encode([
                    'status' => 'error',
                    'message' => 'Error updating compropago order',
                    'short_id' => $verifyInfo->short_id,
                    'reference' => $verifyInfo->order_info->order_id
                ]));
            }

            $ioIn = base64_encode(serialize($orderInfo));
            $ioOut = base64_encode(serialize($verifyInfo));

            $wpdb->insert(
                $wpdb->prefix . 'compropago_transactions',
                array(
                    'date' => $recordTime,
                    'ioIn' => $ioIn,
                    'ioOut' => $ioOut,
                    'orderId' => $row->id,
                    'compropagoId' => $verifyInfo->id,
                    'compropagoStatus' => $verifyInfo->type,
                    'compropagoStatusLast' => $row->compropagoStatus
                )
            );

            die(json_encode([
                'status' => 'success',
                'message' => 'OK - ' . $order->get_status() . ' - ' . $verifyInfo->type,
                'short_id' => $verifyInfo->short_id,
                'reference' => $verifyInfo->order_info->order_id
            ]));
        } else {
            die(json_encode([
                'status' => 'error',
                'message' => 'invalid order: not found ID',
                'short_id' => null,
                'reference' => null
            ]));
        }
    } catch (\Exception $e) {
        die(json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'short_id' => null,
            'reference' => null
        ]));
    }
}

add_action( 'rest_api_init', function () {
    register_rest_route(
        'compropago/',
        'webhook',
        array(
            'methods' => 'POST',
            'callback' => 'compropago_webhook',
        )
    );
});