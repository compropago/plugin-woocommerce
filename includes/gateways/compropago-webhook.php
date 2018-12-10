<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

 use CompropagoSdk\Resources\Payments\Cash;
 use CompropagoSdk\Resources\Payments\Spei;



/**
 * Main webhook function
 */
function cp_webhook()
{
    try
    {
        $request = @file_get_contents('php://input');
        $orderInfo = json_decode($request, true);

        if (empty($orderInfo) || empty($orderInfo['id']))
        {
            $message = 'Invalid request';
            throw new \Exception($message);
        }

        if (!isset($orderInfo['short_id']) || $orderInfo['short_id'] == "000000")
        {
            die(json_encode([
                'status' => 'success',
                'message' => 'OK - TEST',
                'short_id' => null,
                'reference' => null
            ]));
        }

        $order = new WC_Order($orderInfo['order_info']['order_id']);

        switch ($order->get_payment_method())
        {
            case 'cpcash':
                cp_process_cash($order, $orderInfo);
                break;
            case 'cpspei':
                cp_process_spei($order, $orderInfo);
                break;
            case 'crypto':
                break;

            default:
                $message = "Invalid payment method {$order->get_payment_method()}";
                throw new \Exception($message);
        }
    }
    catch (\Exception $e)
    {
        die(json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'short_id' => null,
            'reference' => null
        ]));
    }
}

/**
 * Process cash orders
 * @param WC_Order $order
 * @param mixed $request
 * @throws Exception
 */
function cp_process_cash(WC_Order &$order, $request)
{
    $publickey = get_option('compropago_publickey');
    $privatekey = get_option('compropago_privatekey');
    $mode = get_option('compropago_live') === 'yes';

    if (empty($publickey) || empty($privatekey))
    {
        $message = 'Invalid plugin credentials';
        throw new \Exception($message);
    }

    if ($order->get_meta('compropago_id') != $request['id'])
    {
        $message = 'The order is not from this store';
        throw new \Exception($message);
    }

    # Verify cash order
    $objCash = (new Cash)->withKeys( $publickey, $privatekey );
    $verify = $objCash->verifyOrder( $request['id'] );

    cp_update_order_status($order, $verify['type']);
    $order->save();

    die(json_encode([
        'status'    => 'success',
        'message'   => 'OK - ' . $order->get_status() . ' - CASH',
        'short_id'  => $order->get_meta('compropago_short_id'),
        'reference' => "{$order->get_id()}"
    ]));
}

/**
 * Process spei orders
 * @param WC_Order $order
 * @param mixed $request
 * @throws Exception
 */
function cp_process_spei(WC_Order &$order, $request)
{
    $publickey = get_option('compropago_publickey');
    $privatekey = get_option('compropago_privatekey');

    if (empty($publickey) || empty($privatekey))
    {
        $message = 'Invalid plugin credentials';
        throw new \Exception($message);
    }

    # Verify SPEI order
    $objSpei = (new Spei)->withKeys( $publickey, $privatekey );
    $verify = $objSpei->verifyOrder( $request['id'] );

    $status = $verify['data']['status'];

    switch ($status) {
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
        'status'    => 'success',
        'message'   => 'OK - ' . $order->get_status() . ' - SPEI',
        'short_id'  => $order->get_meta('compropago_short_id'),
        'reference' => "{$order->get_id()}"
    ]));
}

/**
 * Update the status of the order
 * @param WC_Order $order
 * @param string $status
 * @throws Exception
 */
function cp_update_order_status(WC_Order &$order, $status)
{
    $orderStatuses = wc_get_order_statuses();
    $complete_order = get_option('compropago_completed_order');
    $pending_status = get_option('compropago_initial_state');

    switch ($status)
    {
        case 'charge.success':
            if ($complete_order == 'fin')
            {
                $order->payment_complete();
            }
            else
            {
                $new_status = !array_key_exists('wc-processing', $orderStatuses)
                    ? 'processing'
                    : 'wc-processing';

                $order->update_status(
                    $new_status,
                    __( 'ComproPago - Payment Confirmed', 'compropago' )
                );
            }
            break;
        case 'charge.pending':
            $order->update_status(
                $pending_status,
                __( 'ComproPago - Pending Payment', 'compropago' )
            );
            break;
        case 'charge.expired':
            $new_status = !array_key_exists('wc-cancelled', $orderStatuses)
                ? 'cancelled'
                : 'wc-cancelled';

            $order->update_status(
                $new_status,
                __( 'ComproPago - Expired', 'compropago' )
            );
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
