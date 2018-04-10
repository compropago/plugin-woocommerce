<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

use CompropagoSdk\Factory\Factory;
use CompropagoSdk\Client;
use CompropagoSdk\Tools\Request;

/**
 * Main webhook function
 */
function cp_webhook() {
    try {
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            $message = 'WooCommerce is not loaded';
            throw new \Exception($message);
        }

        $request = @file_get_contents('php://input');

        $orderInfo = Factory::getInstanceOf('CpOrderInfo', $request);

        if (empty($orderInfo) || empty($orderInfo->id)) {
            $message = 'Invalid request';
            throw new \Exception($message);
        }

        if($orderInfo->short_id == "000000"){
            die(json_encode([
                'status' => 'success',
                'message' => 'OK - TEST',
                'short_id' => null,
                'reference' => null
            ]));
        }

        $order = new WC_Order($orderInfo->order_info->order_id);

        switch ($order->get_payment_method()) {
            case 'cpcash':
                cp_proccess_cash($order, $orderInfo);
                break;
            case 'cpspei':
                cp_proccess_spei($order, $orderInfo);
                break;
            default:
                $message = "Invalid payment method {$order->get_payment_method()}";
                throw new \Exception($message);
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

/**
 * Proccess cash orders
 * @param WC_Order $order
 * @param mixed $request
 * @throws Exception
 */
function cp_proccess_cash(WC_Order &$order, $request) {
    $publickey = get_option('compropago_publickey');
    $privatekey = get_option('compropago_privatekey');
    $mode = get_option('compropago_live') === 'yes';

    if (empty($publickey) || empty($privatekey)){
        $message = 'Invalid plugin credentials';
        throw new \Exception($message);
    }

    if ($order->get_meta('compropago_id') != $request->id) {
        $message = 'The order is not from this store';
        throw new \Exception($message);
    }

    $client = new Client($publickey, $privatekey, $mode);

    $verify = $client->api->verifyOrder($request->id);
    $status = $verify->type;

    cp_update_order_status($order, $status);
    $order->save();

    die(json_encode([
        'status' => 'success',
        'message' => 'OK - ' . $order->get_status() . ' - CASH',
        'short_id' => $order->get_meta('compropago_short_id'),
        'reference' => "{$order->get_id()}"
    ]));
}

/**
 * Proccess spei orders
 * @param WC_Order $order
 * @param mixed $request
 * @throws Exception
 */
function cp_proccess_spei(WC_Order &$order, $request) {
    $publickey = get_option('compropago_publickey');
    $privatekey = get_option('compropago_privatekey');

    if (empty($publickey) || empty($privatekey)){
        $message = 'Invalid plugin credentials';
        throw new \Exception($message);
    }

    $url = 'https://ms-api.compropago.io/v2/orders/' . $request->id;
    $auth = [
        "user" => $privatekey,
        "pass" => $publickey
    ];

    $response = Request::get($url, $auth);
    $response = json_decode($response);

    if ($response->code != 200) {
        $message = "Can't verify order";
        throw new \Exception($message);
    }

    $verify = $response->data;
    $status = '';

    switch ($verify->status) {
        case 'PENDING':
            $status = 'charge.pending';
            break;
        case 'ACCEPTED':
            $status = 'charge.success';
            break;
        case 'EXPIRED':
            $status = 'charge.expired';
            break;
    }


    cp_update_order_status($order, $status);
    $order->save();

    die(json_encode([
        'status' => 'success',
        'message' => 'OK - ' . $order->get_status() . ' - SPEI',
        'short_id' => $order->get_meta('compropago_short_id'),
        'reference' => "{$order->get_id()}"
    ]));
}

/**
 * Update the status of the order
 * @param WC_Order $order
 * @param string $status
 * @throws Exception
 */
function cp_update_order_status(WC_Order &$order, $status) {
    $orderStatuses = wc_get_order_statuses();
    $complete_order = get_option('compropago_completed_order');
    $pending_status = get_option('compropago_initial_state');

    switch ($status) {
        case 'charge.success':
            if ($complete_order == 'fin') {
                $order->payment_complete();
            } else {
                if (!array_key_exists('wc-processing', $orderStatuses)) {
                    $new_status = 'processing';
                } else {
                    $new_status = 'wc-processing';
                }

                $order->update_status($new_status, __( 'ComproPago - Payment Confirmed', 'compropago' ));
            }
            break;
        case 'charge.pending':
            $order->update_status($pending_status, __( 'ComproPago - Pending Payment', 'compropago' ));
            break;
        case 'charge.expired':
            if (!array_key_exists('wc-cancelled', $orderStatuses)) {
                $new_status = 'cancelled';
            } else {
                $new_status = 'wc-cancelled';
            }

            $order->update_status($new_status, __( 'ComproPago - Expired', 'compropago' ));
            break;
        default:
            $message = 'Invalid status ' . $status;
            throw new \Exception($message);
    }
}


add_action('rest_api_init', function () {
    register_rest_route(
        'compropago/',
        'webhook',
        array(
            'methods' => 'POST',
            'callback' => 'cp_webhook',
        )
    );
});