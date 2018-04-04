<?php
/**
 * @author Eduardo Aguilar <dante.aguilar41@gmail.com>
 */

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * Render the ComproPago Order
 * @param mixed $order_id
 */
function cp_receipt($order_id) {
    global $wpdb;

    $compropagoOrder = $wpdb->prefix . 'compropago_orders';

    $query = "SELECT * FROM $compropagoOrder WHERE storeOrderId = '$order_id'";

    $mylink = $wpdb->get_row($query);

    if ($mylink) {
        $template = __DIR__ . '/../../templates/receipt.html';

        $receipt = file_get_contents($template);
        $receipt = str_replace(':cpid:', $mylink->compropagoId, $receipt);

        echo $receipt;
    } else {
        echo "Fallo al recuperar el link";
    }
}

add_action('woocommerce_thankyou', 'cp_receipt', 1);