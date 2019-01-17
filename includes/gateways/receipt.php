<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

require_once ABSPATH . 'wp-admin/includes/upgrade.php';


/**
 * Render the ComproPago Order
 * @param mixed $order_id
 */
function cp_receipt($order_id)
{
    $order          = new WC_Order($order_id);
    $compropagoId   = $order->get_meta('compropago_id');

    if (!empty($compropagoId)) {
        $template   = __DIR__ . '/../../templates/receipt.html';
        $receipt    = file_get_contents( $template) ;
        $receipt    = str_replace(':cpid:', $compropagoId, $receipt);

        echo $receipt;
    }
}

add_action('woocommerce_thankyou', 'cp_receipt', 1);
